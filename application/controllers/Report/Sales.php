<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales extends CI_Controller
{
    private $Date;
    private $DateTime;
    private $layout = 'layout';

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->Date = date("Y-m-d");
        $this->DateTime = date("Y-m-d H:i:s");
        $this->load->model('m_helper', 'help');
        $this->load->model('m_DataTable', 'M_Datatables');
    }

    public function sales_order_report()
    {
        $this->data['page_title'] = "Sales Order Report";
        $this->data['page_content'] = "Report/Sales/sales_order_report";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Report/Sales/sales_order_report.js"></script>';

        $this->data['qAccount'] =  $this->db->query("
        SELECT TAccount.Account_Id,
        TAccount.Account_Name
        FROM TAccount WHERE
        (TAccount.Cust_FG = 1
        OR TAccount.Cust_RM = 1
        OR TAccount.Cust_Ast = 1
        OR TAccount.Cust_SP = 1
        OR TAccount.Cust_SF = 1)
        AND	TAccount.Category_ID IN (SELECT DISTINCT CATEGORY_ID FROM TDATAGROUPACCOUNT WHERE DATAGROUP_ID IN (20,86))
        AND TAccount.Company_ID = 2
        ORDER BY TAccount.Account_Name");

        $this->data['qCurrency'] = $this->db->query("SELECT Currency_ID,currency_symbol
        FROM TCurrency
        WHERE status = 1");

        $this->data['SelCurr'] = $this->input->get('SelCurr');


        $this->load->view($this->layout, $this->data);
    }

    public function Rpt_sales_order()
    {
        $until = $this->input->get("until");;
        $newUntil = date('Y-m-d', strtotime($until . ' +1 day'));

        $this->data['customer'] = $this->input->get("customer");
        $this->data['sales_type'] = $this->input->get("sales_type");
        $this->data['from'] = $this->input->get("from");
        $this->data['until'] = $newUntil;
        $this->data['rdocurrency'] = $this->input->get("rdocurrency");
        $this->data['selCurrency'] = $this->input->get("selCurrency");
        $this->data['TxtUSD'] = $this->input->get("TxtUSD");
        $this->data['TxtEUR'] = $this->input->get("TxtEUR");
        $this->data['TxtAUD'] = $this->input->get("TxtAUD");
        $this->data['TxtSGD'] = $this->input->get("TxtSGD");
        $this->data['TxtKRW'] = $this->input->get("TxtKRW");
        $this->data['TxtJPY'] = $this->input->get("TxtJPY");
        $this->data['TxtGBP'] = $this->input->get("TxtGBP");
        $this->data['report_type'] = $this->input->get("report_type");

        $this->load->view('Report/Sales/Rpt_sales_order', $this->data);
    }

    public function ostpo_rawmaterial()
    {
        $this->data['page_title'] = "Report Raw Material vs Outstanding PO";
        $this->data['page_content'] = "Report/Sales/ostpo_rawmaterial";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Report/Sales/ostpo_rawmaterial.js"></script>';

        $this->data['qAccount'] =  $this->db->query("
        SELECT TAccount.Account_Id,
        TAccount.Account_Name
        FROM TAccount WHERE
        (TAccount.Cust_FG = 1
        OR TAccount.Cust_RM = 1
        OR TAccount.Cust_Ast = 1
        OR TAccount.Cust_SP = 1
        OR TAccount.Cust_SF = 1)
        AND	TAccount.Category_ID IN (SELECT DISTINCT CATEGORY_ID FROM TDATAGROUPACCOUNT WHERE DATAGROUP_ID IN (20,86))
        AND TAccount.Company_ID = 2
        ORDER BY TAccount.Account_Name");

        $this->load->view($this->layout, $this->data);
    }

    public function Rpt_ostpo_rawmaterial()
    {
        $until = $this->input->get("until");
        $from = $this->input->get("from");
        $newUntil = date('Y-m-d', strtotime($until . ' +1 day'));
        $sales_type = $this->input->get("sales_type");
        $customer = $this->input->get("customer");
        $this->data['customer'] = $customer;
        $this->data['sales_type'] = $sales_type;
        $this->data['from'] = $this->input->get("from");
        $this->data['until'] = $newUntil;
        $this->data['report_type'] = $this->input->get("report_type");

        $sql = "SELECT MainQ.Item_SO_Times,
                MainQ.dimension_id,
                MainQ.QTY_PO_PERITEM,
                MainQ.qtyDeliver,
                MainQ.RemainingQty,
                tppicitembom_detail.bom_code,
                BOMHDR.ITEM_CODE,
                tppicitembom_detail.rm_code,
                tppicitembom_detail.rm_qty,
                MainQ.RemainingQty * tppicitembom_detail.rm_qty as Qty_Needed_ForSO,
                tppicitembom_detail.rm_unittypeid,
                tppicitembom_detail.bom_id,
                tppicitembom_detail.dimension_id,
                case
                    when titemcompany.itemcategory_id in (49,50,51,52,53,62,63,64,65,66)
                    then
                        (select	top 1 tppicitembom.cost
                        from	tppicitembom
                        where	tppicitembom_detail.rm_code = tppicitembom.item_code order by last_update desc )
                    else
                        tppicitembom_detail.cost
                end as cost,
                case
                    when titemcompany.itemcategory_id in (49,50,51,52,53,62,63,64,65,66)
                    then
                        (select	top 1 tppicitembom.currency_id
                        from	tppicitembom
                        where	tppicitembom_detail.rm_code = tppicitembom.item_code order by last_update desc)
                    else
                        isnull(tppicitembom_detail.currency_id, 'IDR')
                end as currency_id,
                -- tppicitembom_detail.group,
                tppicitembom_detail.account_id,
                tppicitembom_detail.item_convertion,
                tppicitembom_detail.is_accessories,
                tppicitembom_detail.is_expensive_parts,
                tppicitembom_detail.loss_percentage,
                tppicitembom_detail.comp_loss_percentage,
                titem.item_name,
                isnull(cast(titem.item_height as varchar), '-') + ' x ' +
                isnull(cast(titem.item_width as varchar), '-') + ' x ' +
                isnull(cast(titem.item_length as varchar), '-') as item_size,
                titem.customfield1 as type,
                titem.item_size as brand,
                tgscolor.color_name,
                titemdimension.dimension_name,
                taccunittype.unit_name,
                taccount.account_name
                    from tppicitembom_detail
                    inner join titem on tppicitembom_detail.rm_code = titem.item_code
                    inner join TPPICITEMBOM AS BOMHDR on BOMHDR.BOM_CODE = tppicitembom_detail.bom_code
                    inner join titemcompany on titemcompany.item_code = titem.item_code and titemcompany.dimension_id = tppicitembom_detail.dimension_id
                    inner join titemdimension on titemdimension.dimension_id = titemcompany.dimension_id
                    inner join taccunittype on taccunittype.unit_type_id = titem.unit_type_id
                    inner join (SELECT SODTL.Item_Code, 
                        COUNT(SODTL.Item_Code) AS Item_SO_Times, 
                        SODTL.dimension_id, 
                        SUM(SODTL.Qty) AS QTY_PO_PERITEM, 
                        ISNULL(
                            (
                                SELECT 
                                    SUM(taccSN_Item.Qty) 
                                FROM 
                                    taccSN_Header 
                                    INNER JOIN taccSN_Item ON taccSN_Item.SN_number = taccSN_Header.SN_number
                                WHERE 
                                    ISNULL(TAccSN_Header.isVoid, 0) = 0 
                                    AND taccSN_Item.Item_code = SODTL.item_code 
                                    AND TAccSN_Item.SO_number IN (
                                        SELECT 
                                            SOHDR2.SO_Number 
                                        FROM 
                                            dbsai_erp_uat.dbo.TAccSO_Header AS SOHDR2
                                        WHERE 
                                            SOHDR2.SO_Date >= {d '$from'}
                                            AND SOHDR2.SO_Date < {d '$newUntil'} 
                                        GROUP BY 
                                            SOHDR2.SO_Number
                                    )
                                    AND taccSN_Item.dimension_id = SODTL.dimension_id
                                    AND taccSN_Header.Approval_Status = 3
                            ), 
                            0
                        ) AS qtyDeliver,
                        SUM(SODTL.Qty) - ISNULL(
                            (
                                SELECT 
                                    SUM(taccSN_Item.Qty) 
                                FROM 
                                    taccSN_Header 
                                    INNER JOIN taccSN_Item ON taccSN_Item.SN_number = taccSN_Header.SN_number
                                WHERE 
                                    ISNULL(TAccSN_Header.isVoid, 0) = 0 
                                    AND taccSN_Item.Item_code = SODTL.item_code 
                                    AND TAccSN_Item.SO_number IN (
                                        SELECT 
                                            SOHDR2.SO_Number 
                                        FROM 
                                            dbsai_erp_uat.dbo.TAccSO_Header AS SOHDR2
                                        WHERE 
                                            SOHDR2.SO_Date >= {d '$from'}
                                            AND SOHDR2.SO_Date < {d '$newUntil'} 
                                        GROUP BY 
                                            SOHDR2.SO_Number
                                    )
                                    AND taccSN_Item.dimension_id = SODTL.dimension_id
                                    AND taccSN_Header.Approval_Status = 3
                            ), 
                            0) AS RemainingQty
                    FROM dbsai_erp_uat.dbo.TAccSO_Header AS SOHDR
                        INNER JOIN TAccSO_Detail AS SODTL ON SODTL.SO_Number = SOHDR.SO_Number
                        inner JOIN tppicitembom AS bom1 on SODTL.Item_Code = bom1.ITEM_CODE
                    WHERE 
                        SOHDR.SO_Date >= {d '$from'}
                        AND SOHDR.SO_Date < {d '$newUntil'} ";
        if ($sales_type != 'ALL') {
            $sql .= "AND TAccSO_Header.isExport = '$sales_type' ";
        }
        if ($customer != 'ALL') {
            $sql .= "AND SOHDR.Account_ID = '$customer' ";
        }
        $sql .= " GROUP BY SODTL.Item_Code, 
                SODTL.dimension_id) AS MainQ ON BOMHDR.ITEM_CODE = MainQ.Item_Code
                left join taccount on taccount.account_id = tppicitembom_detail.account_id
                left join tgscolor on tgscolor.color_code = titem.color_code
                WHERE MainQ.RemainingQty > 0
                order by BOMHDR.ITEM_CODE, tppicitembom_detail.rm_code ASC";
        $this->data['SqlBomPerOstPO'] = $this->db->query($sql);

        $this->load->view('Report/Sales/Rpt_ostpo_rawmaterial', $this->data);
    }

    public function Rpt_ostpo_summary_rawmaterial()
    {
        var_dump($this->input->get());
        die;
    }

    public function customer_transaction_report()
    {
        $this->data['page_title'] = "Customer Report";

        $this->load->view('Report/Sales/customer_report_view', $this->data);
    }
}
