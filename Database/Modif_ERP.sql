DROP TABLE dbsai_erp_uat.dbo.TmstTrxSettingSteppApprovalCbr;
CREATE TABLE dbsai_erp_uat.dbo.TmstTrxSettingSteppApprovalCbr (
	SysId bigint IDENTITY(1,1) NOT NULL,
	Setting_Approval_Code varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	Staff bit DEFAULT 0 NOT NULL,
	Staff_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	Chief bit DEFAULT 0 NOT NULL,
	Chief_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	AsstManager bit DEFAULT 0 NOT NULL,
	AsstManager_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	Manager bit DEFAULT 0 NOT NULL,
	Manager_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	SeniorManager bit DEFAULT 0 NOT NULL,
	SeniorManager_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	GeneralManager bit DEFAULT 0 NOT NULL,
	GeneralManager_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	Additional bit DEFAULT 0 NOT NULL,
	Additional_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	Director bit DEFAULT 0 NOT NULL,
	Director_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	PresidentDirector bit DEFAULT 1 NOT NULL,
	PresidentDirector_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	FinanceDirector bit DEFAULT 1 NOT NULL,
	FinanceDirector_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	LastUpdated_at datetime NOT NULL,
	Doc_Legitimate_Pos_On varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	FinanceManager bit DEFAULT 1 NOT NULL,
	FinanceManager_Person varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	LastUpdated_by varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	Is_Active bit DEFAULT 1 NOT NULL,
	CONSTRAINT TmstTrxSettingSteppApprovalCbr_PK PRIMARY KEY (SysId),
	CONSTRAINT UQ_Setting_Approval_Code UNIQUE (Setting_Approval_Code)
);


-- dbsai_erp_uat.dbo.Tmst_User_NonHR definition

-- Drop table

DROP TABLE dbsai_erp_uat.dbo.Tmst_User_NonHR;
CREATE TABLE dbsai_erp_uat.dbo.Tmst_User_NonHR (
	Dir_ID bigint IDENTITY(1,1) NOT NULL,
	Emp_No varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	UserID int NOT NULL,
	Pos_Name varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	Division_Name varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	First_Name varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	is_active bit DEFAULT 1 NOT NULL
);

CREATE TABLE dbsai_erp_uat.dbo.Ttrx_Assignment_Approval_User (
	SysId bigint IDENTITY(1,1) NOT NULL,
	UserName_Employee varchar(50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	SysId_Approval int NOT NULL,
	Created_at datetime NOT NULL,
	Created_by varchar(50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	CONSTRAINT PK_Assignment_Approval_User PRIMARY KEY (SysId),
	CONSTRAINT UQ_Assignment_User_Approval UNIQUE (UserName_Employee,SysId_Approval)
);



DROP VIEW ERPQview_User_Employee;
CREATE VIEW dbo.ERPQview_User_Employee AS
SELECT tuser.User_ID, First_Name, User_Name, User_Password, User_Hints, User_Answer, Site_ID, Language_ID, User_Type, Forget_Password,
security_password, WH_ID, is_Passive, User_Status, flag_eula, eula_date, LoginFail_Ip, User_Ldap, User_Pass_Txt,
Middle_Name, Last_Name, Gender, Date_of_Birth, Email_Address, Address1, Address2, City, State, Country_ID, Postal_Code, 
Fax, Phone, HandPhone, Web_Site, GMT_ID, Signature, POP3_Address, Mail_Acc_User_ID, Mail_Acc_User_Password, Leave_Mail_on_Server, Upload_Extra_Size,
Upload_Extra_Type, Port_Number, Category_id, Server_Time_Out, User_Title, User_NickName, Anniversary
FROM dbsai_erp_uat.dbo.tuser
inner join TUserPersonal on tuser.User_ID = TUserPersonal.User_ID;

DROP VIEW QviewSettingStepApproval;
CREATE VIEW QviewSettingStepApproval 
AS
SELECT
    T1.SysId, 
    T1.Setting_Approval_Code, 
    T1.Staff, T1.Staff_Person, 
    T1.Chief, T1.Chief_Person, 
    T1.AsstManager, T1.AsstManager_Person, 
    T1.Manager, T1.Manager_Person, 
    T1.SeniorManager, T1.SeniorManager_Person, 
    T1.GeneralManager, T1.GeneralManager_Person, 
    T1.Additional, T1.Additional_Person, 
    T1.Director, T1.Director_Person, 
    T1.PresidentDirector, T1.PresidentDirector_Person, 
    T1.FinanceDirector, T1.FinanceDirector_Person,
    T1.FinanceManager, T1.FinanceManager_Person, 
    T1.Doc_Legitimate_Pos_On, 
    T1.LastUpdated_at, T1.LastUpdated_by,
	T1.Is_Active,
    C.First_Name AS Chief_Name,
    AM.First_Name AS AsstManager_Name,
    M.First_Name AS Manager_Name,
    SM.First_Name AS SeniorManager_Name,
    GM.First_Name AS GeneralManager_Name,
    A.First_Name AS Additional_Name,
    D.First_Name AS Director_Name,
    PD.First_Name AS PresidentDirector_Name,
    FD.First_Name AS FinanceDirector_Name,
    FM.First_Name AS FinanceManager_Name
FROM 
    dbsai_erp_uat.dbo.TmstTrxSettingSteppApprovalCbr AS T1
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS C ON T1.Chief_Person = C.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS AM ON T1.AsstManager_Person = AM.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS M ON T1.Manager_Person = M.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS SM ON T1.SeniorManager_Person = SM.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS GM ON T1.GeneralManager_Person = GM.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS A ON T1.Additional_Person = A.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS D ON T1.Director_Person = D.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS PD ON T1.PresidentDirector_Person = PD.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS FD ON T1.FinanceDirector_Person = FD.User_Name
LEFT JOIN dbsai_erp_uat.dbo.ERPQview_User_Employee AS FM ON T1.FinanceManager_Person = FM.User_Name;

