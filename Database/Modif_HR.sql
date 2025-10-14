-- dbo.HRQviewEmployeeDetail source

-- dbo.HRQviewEmployeeDetail source

-- dbo.HRQviewEmployeeDetail source

-- dbo.HRQviewEmployeeDetail source
DROP VIEW dbo.HRQviewEmployeeDetail;
CREATE VIEW dbo.HRQviewEmployeeDetail AS
SELECT  distinct
				THRMEmpPersonalData.User_id,
				THRMEmpPersonalData.Emp_ID, 
				THRMEmpCompany.Emp_No, 
				THRMEmpPersonalData.First_Name, 
				THRMEmpPersonalData.Middle_Name, 
				THRMEmpPersonalData.Last_Name,
				THRMEmpPersonalData.Birth_Place,
				THRMEmpPersonalData.Date_Of_Birth,
				THRMEmpPersonalData.EMP_IMAGE,
				THRMEmpPersonalData.MOBILE_PHONE,
				THRMEmpPersonalData.Email,
                THRMEmpPersonalData.IS_VERIFIED,
				THRMEmpCompany.Start_Date,
				THRMEmpCompany.End_Date,
				THRMEmpCompany.jobstatuscode,
				THRMEmpCompany.EMPLOYMENT_CODE,
				THRMEmpCompany.LEVEL_CODE, 
				THRMPayLevelCat.PAYLEVELCAT_NAME, 
				THRMEmpCompany.Edit_Status, 
				THRMEmpCompany.Edit_Status_Group,
				THRMEmpCompany.Cost_Center,
				THRMPayLevel.LevelName,
				THRMPosition.Position_ID,THRMPosition.Division_ID,
				THRMPosition.Position_Name_en as Pos_Name,
				TDiv.Position_Name_en as Division_Name,
				isnull(TUpDiv.Position_Name_en, TDiv.Position_Name_en) as UpDivision_Name,
				(SELECT CostCenter_Name_en FROM THRMCostCenter WHERE CostCenter_Code = THRMEmpCompany.Cost_Center AND COMPANY_ID=THRMEMPCOMPANY.COMPANY_ID) as costcenter_name,
				THRMEMPLOYMENTSTATUS.EMPLOYMENTSTATUS_NAME_en AS EMPLOYMENTSTATUS_NAME
				,thrmworklocation.worklocation_name
		FROM 	THRMEmpPersonalData 
			INNER JOIN THRMEmpCompany ON THRMEmpCompany.Emp_ID = THRMEmpPersonalData.Emp_ID
			LEFT OUTER JOIN THRMPosition ON THRMEmpCompany.Position_ID = THRMPosition.Position_ID
			JOIN THRMPosition TDiv ON THRMPosition.Division_Id = TDiv.Position_Id
			LEFT OUTER JOIN THRMPosition TUpDiv ON TDiv.Position_Parent = TUpDiv.Position_Id 
			LEFT OUTER JOIN THRMPayLevelCat ON THRMPayLevelCat.PayLevelCat_Code = THRMEmpCompany.PayLevelCat_Code AND THRMEmpCompany.Company_ID = THRMPayLevelCat.Company_ID
			LEFT OUTER JOIN THRMPaylevel ON THRMEmpCompany.level_code = THRMPaylevel.levelcode	AND THRMEmpCompany.Company_ID = THRMPayLevel.Company_ID
			LEFT OUTER JOIN THRMEMPLOYMENTSTATUS ON THRMEMPCOMPANY.EMPLOYMENT_CODE = THRMEMPLOYMENTSTATUS.EMPLOYMENTSTATUS_CODE
				LEFT OUTER JOIN THRMWorkLocation  ON THRMEmpCompany.WorkLocation_Code = THRMWorkLocation.WorkLocation_Code AND THRMEmpCompany.Company_ID = THRMWorkLocation.Company_ID
				AND thrmworklocation.company_id =  73 
		WHERE	THRMEmpCompany.Company_ID =  73
				AND THRMEmpCompany.WORKLOCATION_CODE = 01;
--				AND (THRMEmpCompany.End_Date > GETDATE() OR THRMEmpCompany.End_Date IS NULL);