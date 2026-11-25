<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;

class EmailVprAudit extends CI_Controller
{
    public function send()
    {
        if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', '::ffff:127.0.0.1'], true)) {
            return $this->respond('Scheduler hanya dapat dipanggil dari server ESBA.', 403);
        }
        if ($this->input->method(true) !== 'GET') {
            $this->output->set_header('Allow: GET');
            return $this->respond('Gunakan GET.', 405);
        }

        $today = new DateTimeImmutable('today', new DateTimeZone('Asia/Jakarta'));
        if ($today->format('N') !== '1') {
            return $this->respond('Pengiriman laporan VPR dijadwalkan setiap Senin.');
        }

        $day = $today->format('Y-m-d');
        $lock = @fopen(APPPATH . 'logs/email-vpr-audit-' . $day . '.lock', 'c+');
        if ($lock === false) {
            return $this->respond('Catatan pengiriman laporan tidak dapat dibuka.', 500);
        }
        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            fclose($lock);
            return $this->respond('Pengiriman laporan sedang berjalan.', 409);
        }

        try {
            if (trim((string) stream_get_contents($lock)) === 'SENT') {
                return $this->respond('Laporan VPR sudah dikirim pada ' . $day . '.');
            }

            $start = $today->modify('first day of this month');
            $startDate = $start->format('Y-m-d');
            $this->load->model('Vpr_audit_model');
            $this->load->library('Vpr_audit_excel');
            $report = $this->Vpr_audit_model->report($start, $today);
            $attachment = $this->vpr_audit_excel->render($report['reports'], $startDate, $day);
            $mail = $this->createMail($startDate, $day, count($report['reports']), $attachment);
            $mail->send();

            rewind($lock);
            if (fwrite($lock, 'SENT') !== 4 || !fflush($lock)) {
                log_message('error', 'Scheduler EmailVprAudit: SMTP accepted the email, but delivery status could not be saved for ' . $day);
                return $this->respond('Email diterima SMTP, tetapi status pengiriman gagal dicatat. Periksa sebelum menjalankan ulang.', 500);
            }
            return $this->respond('Laporan VPR periode ' . $startDate . ' s.d. ' . $day . ' berhasil dikirim.');
        } catch (Throwable $e) {
            log_message('error', 'Scheduler EmailVprAudit: ' . $e->getMessage());
            return $this->respond('Pengiriman laporan VPR gagal. Periksa log ESBA.', 500);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function createMail(string $start, string $end, int $findings, string $attachment): PHPMailer
    {
        $this->config->load('email', true);
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = (string) $this->config->item('smtp_host', 'email');
        $mail->Port = (int) $this->config->item('smtp_port', 'email');
        $mail->SMTPAuth = true;
        $mail->Username = (string) $this->config->item('smtp_user', 'email');
        $mail->Password = (string) $this->config->item('smtp_pass', 'email');
        $mail->SMTPSecure = (string) $this->config->item('smtp_crypto', 'email');
        $mail->Timeout = (int) ($this->config->item('smtp_timeout', 'email') ?: 30);
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($mail->Username, 'ESBA Reports');

        $mail->addAddress('siky@samick.co.id');
        foreach (['account@samick.co.id', 'accountsam@samick.co.id', 'logistics@samick.co.id'] as $recipient) {
            $mail->addCC($recipient);
        }
        $period = $start . ' s.d. ' . $end;
        $mail->Subject = 'Report Akses Vendor Pricing Request | ' . $period;
        $mail->Body = "Yth. Bapak/Ibu,\r\n\r\n"
            . 'Terlampir laporan akses Vendor Pricing Request untuk periode ' . $period . ".\r\n"
            . 'Jumlah VPR yang perlu ditinjau: ' . $findings . ".\r\n\r\n"
            . "Terima kasih.\r\nESBA";
        $mail->addStringAttachment($attachment, Vpr_audit_excel::filename($start, $end),
            PHPMailer::ENCODING_BASE64, Vpr_audit_excel::MIME_TYPE);
        return $mail;
    }

    private function respond(string $message, int $status = 200)
    {
        return $this->output->set_status_header($status)
            ->set_header('Cache-Control: no-store')
            ->set_content_type('text/plain', 'utf-8')
            ->set_output($message . "\n");
    }
}
