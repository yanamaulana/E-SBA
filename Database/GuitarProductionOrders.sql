/* Q018: parameterized production period, SQL Server 2014.
   Bind inclusive first-of-month dates (from, to), as in Q012.
   BuyerName uses the documented Q017 reporting buyer rules.
   Customer remains the original ERP account name. */
;WITH Months AS (
    SELECT CAST(? AS date) AS PeriodStart
    UNION ALL
    SELECT DATEADD(month, 1, PeriodStart)
    FROM Months
    WHERE PeriodStart < CAST(? AS date)
), OrderKeys AS (
    SELECT H.SO_Number, H.Production_year, H.Production_month,
           D.Item_Code, D.Dimension_ID,
           UPPER(LEFT(D.Item_Code, 2)) AS GuitarType,
           SUM(CAST(D.Qty AS decimal(19,4))) AS OrderQty,
           SUM(CAST(D.TotalPrice AS decimal(19,4))) AS OrderAmt
    FROM dbo.TAccSO_Header AS H
    INNER JOIN dbo.TAccSO_Detail AS D ON D.SO_Number = H.SO_Number
    WHERE H.Production_month BETWEEN 1 AND 12
      AND EXISTS (SELECT 1 FROM Months AS C
                  WHERE YEAR(C.PeriodStart) = H.Production_year
                    AND MONTH(C.PeriodStart) = H.Production_month)
      AND H.Company_ID = 2
      AND H.ItemCategoryType = 'FG'
      AND ISNULL(H.IsNotActive, 0) = 0
      AND H.Approval_Status = 3
      AND H.isExport = 1
      AND H.Currency_ID = 'USD'
      AND UPPER(LTRIM(ISNULL(H.PO_NumCustomer, ''))) NOT LIKE 'SG%'
      AND UPPER(LTRIM(ISNULL(H.PO_NumCustomer, ''))) NOT LIKE 'GS%'
      AND (D.Item_Code LIKE 'AG%' OR D.Item_Code LIKE 'EG%')
      AND EXISTS (SELECT 1 FROM dbo.TAccount AS A
                  WHERE A.Account_ID = H.Account_ID)
    GROUP BY H.SO_Number, H.Production_year, H.Production_month,
             D.Item_Code, D.Dimension_ID, UPPER(LEFT(D.Item_Code, 2))
), ShipmentKeys AS (
    SELECT S.SO_Number, S.Item_Code, S.Dimension_ID,
           SUM(CAST(S.Qty AS decimal(19,4))) AS DeliveredQty
    FROM dbo.TAccSN_Item AS S
    INNER JOIN dbo.TAccSN_Header AS SH ON SH.SN_Number = S.SN_Number
    WHERE ISNULL(SH.isVoid, 0) = 0 AND SH.Approval_Status = 3
      AND EXISTS (SELECT 1 FROM OrderKeys AS O
                  WHERE O.SO_Number = S.SO_Number
                    AND O.Item_Code = S.Item_Code
                    AND (O.Dimension_ID = S.Dimension_ID OR
                         (O.Dimension_ID IS NULL AND S.Dimension_ID IS NULL)))
    GROUP BY S.SO_Number, S.Item_Code, S.Dimension_ID
), Matched AS (
    SELECT O.*,
           CAST(O.OrderQty AS decimal(28,4)) -
           CAST(ISNULL(S.DeliveredQty, 0) AS decimal(28,4)) AS BalanceQty
    FROM OrderKeys AS O
    LEFT JOIN ShipmentKeys AS S ON O.SO_Number = S.SO_Number
        AND O.Item_Code = S.Item_Code
        AND (O.Dimension_ID = S.Dimension_ID OR
             (O.Dimension_ID IS NULL AND S.Dimension_ID IS NULL))
), OrderTotals AS (
    SELECT SO_Number, Production_year, Production_month,
           SUM(CASE WHEN GuitarType = 'AG' THEN OrderQty ELSE 0 END) AS AG_OrderQty,
           SUM(CASE WHEN GuitarType = 'AG' THEN BalanceQty ELSE 0 END) AS AG_BalanceQty,
           SUM(CASE WHEN GuitarType = 'EG' THEN OrderQty ELSE 0 END) AS EG_OrderQty,
           SUM(CASE WHEN GuitarType = 'EG' THEN BalanceQty ELSE 0 END) AS EG_BalanceQty,
           SUM(CASE WHEN GuitarType = 'AG' THEN OrderAmt ELSE 0 END) AS AG_POAmt,
           SUM(CASE WHEN GuitarType = 'EG' THEN OrderAmt ELSE 0 END) AS EG_POAmt
    FROM Matched
    GROUP BY SO_Number, Production_year, Production_month
)
SELECT CAST(H.SO_Date AS date) AS [Order Receiving Date],
       O.Production_year AS [Prod Year],
       CAST(H.Due_date AS date) AS [Expected Ship. Date],
       O.Production_month AS [Prod Month],
       -- CAST('' AS nvarchar(50)) AS [Customer Code],
       A.Account_Name AS [Customer],
       H.PO_NumCustomer AS [P/O],
       ISNULL(O.AG_OrderQty, 0) AS [AG Order],
       ISNULL(O.AG_BalanceQty, 0) AS [AG Balance],
       ISNULL(O.EG_OrderQty, 0) AS [EG Order],
       ISNULL(O.EG_BalanceQty, 0) AS [EG Balance],
       CAST('' AS nvarchar(50)) AS [ETC],
       ISNULL(O.AG_POAmt, 0) AS [AG PO Amt],
       ISNULL(O.EG_POAmt, 0) AS [EG PO Amt],
       CAST(ISNULL(O.AG_POAmt, 0) AS decimal(28,4)) +
       CAST(ISNULL(O.EG_POAmt, 0) AS decimal(28,4)) AS [Total Amount],
       CAST('' AS nvarchar(255)) AS [Remark],
       O.SO_Number AS [SO_Number],
       H.Account_ID AS [Account_ID],
       CASE
           WHEN UPPER(LTRIM(RTRIM(ISNULL(A.Account_Name, '')))) LIKE '%GIBSON%'
             OR UPPER(LTRIM(RTRIM(ISNULL(A.Account_Name, '')))) LIKE 'GB %' THEN 'GIBSON'
           WHEN UPPER(LTRIM(RTRIM(ISNULL(A.Account_Name, '')))) LIKE '%FENDER%' THEN 'FENDER'
           WHEN H.Account_ID IN (9841, 256, 236) THEN 'ESP'
           WHEN UPPER(LTRIM(RTRIM(ISNULL(A.Account_Name, '')))) LIKE '%YAMAHA%' THEN 'YAMAHA'
           WHEN UPPER(LTRIM(RTRIM(ISNULL(A.Account_Name, '')))) LIKE '%GUITAR CENTER%' THEN 'GUITAR CENTER'
           WHEN UPPER(LTRIM(RTRIM(ISNULL(A.Account_Name, '')))) LIKE '%GUILD%' THEN 'GUILD'
           WHEN UPPER(LTRIM(RTRIM(ISNULL(A.Account_Name, '')))) LIKE '%ZAGER%' THEN 'ZAGER'
           ELSE COALESCE(NULLIF(LTRIM(RTRIM(A.Account_Name)), ''),
                         '(Unnamed account ' + CONVERT(nvarchar(50), H.Account_ID) + ')')
       END AS [BuyerName]
FROM OrderTotals AS O
INNER JOIN dbo.TAccSO_Header AS H ON H.SO_Number = O.SO_Number
    AND H.Production_year = O.Production_year
    AND H.Production_month = O.Production_month
INNER JOIN dbo.TAccount AS A ON A.Account_ID = H.Account_ID
ORDER BY O.Production_year, O.Production_month,
         [BuyerName], A.Account_Name, H.SO_Date, H.PO_NumCustomer, O.SO_Number
OPTION (MAXRECURSION 120);
