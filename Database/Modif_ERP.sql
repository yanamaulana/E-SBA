-- dbsai_erp_uat.dbo.TmstTrxSettingSteppApprovalCbr definition

-- Drop table

-- DROP TABLE dbsai_erp_uat.dbo.TmstTrxSettingSteppApprovalCbr;

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
	CONSTRAINT TmstTrxSettingSteppApprovalCbr_PK PRIMARY KEY (SysId),
	CONSTRAINT UQ_Setting_Approval_Code UNIQUE (Setting_Approval_Code)
);


-- dbsai_erp_uat.dbo.Tmst_User_NonHR definition

-- Drop table

-- DROP TABLE dbsai_erp_uat.dbo.Tmst_User_NonHR;

CREATE TABLE dbsai_erp_uat.dbo.Tmst_User_NonHR (
	Dir_ID bigint IDENTITY(1,1) NOT NULL,
	Emp_No varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	UserID int NOT NULL,
	Pos_Name varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	Division_Name varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	First_Name varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	is_active bit DEFAULT 1 NOT NULL
);



-- dbo.ERPQview_User_Employee source

-- dbo.ERPQview_User_Employee source

CREATE OR ALTER VIEW dbo.ERPQview_User_Employee AS
SELECT tuser.User_ID, First_Name, User_Name, User_Password, User_Hints, User_Answer, Site_ID, Language_ID, User_Type, Forget_Password,
security_password, WH_ID, is_Passive, User_Status, flag_eula, eula_date, LoginFail_Ip, User_Ldap, User_Pass_Txt,
Middle_Name, Last_Name, Gender, Date_of_Birth, Email_Address, Address1, Address2, City, State, Country_ID, Postal_Code, 
Fax, Phone, HandPhone, Web_Site, GMT_ID, Signature, POP3_Address, Mail_Acc_User_ID, Mail_Acc_User_Password, Leave_Mail_on_Server, Upload_Extra_Size,
Upload_Extra_Type, Port_Number, Category_id, Server_Time_Out, User_Title, User_NickName, Anniversary
FROM dbsai_erp_uat.dbo.tuser
inner join TUserPersonal on tuser.User_ID = TUserPersonal.User_ID;;