-- Cashbook header attachments. Safe to run again; existing data is preserved.
SET XACT_ABORT ON;
BEGIN TRANSACTION;
IF OBJECT_ID('dbo.Ttrx_Cashbook_Attachment', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.Ttrx_Cashbook_Attachment (
        Attachment_ID bigint IDENTITY(1,1) NOT NULL PRIMARY KEY,
        Company_ID int NOT NULL,
        JournalH_Code varchar(50) NOT NULL,
        CashBookType varchar(10) NOT NULL,
        Header_Date date NOT NULL,
        Original_Name nvarchar(255) NOT NULL,
        Relative_Path varchar(400) NOT NULL,
        Mime_Type varchar(50) NOT NULL,
        File_Size bigint NOT NULL,
        Notes nvarchar(2000) NULL,
        Created_By nvarchar(100) NOT NULL,
        Created_At datetime2(0) NOT NULL CONSTRAINT DF_CashbookAttachment_CreatedAt DEFAULT SYSDATETIME(),
        CONSTRAINT CK_CashbookAttachment_Type CHECK (CashBookType IN ('BR','CR','BD','CD')),
        CONSTRAINT CK_CashbookAttachment_Size CHECK (File_Size > 0),
        CONSTRAINT UQ_CashbookAttachment_Path UNIQUE (Relative_Path)
    );
    CREATE INDEX IX_CashbookAttachment_Header
        ON dbo.Ttrx_Cashbook_Attachment (Company_ID, JournalH_Code, Attachment_ID);
END;
COMMIT TRANSACTION;
