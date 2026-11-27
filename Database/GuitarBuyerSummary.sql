/* Q017: Guitar Buyer Summary, SQL Server 2014.
   Read-only; two bound inclusive production-month dates (YYYYMM01).
   One row per reporting buyer and production month. */
;WITH Months AS (
    SELECT CAST(? AS date) AS PeriodStart
    UNION ALL
    SELECT DATEADD(month, 1, PeriodStart)
    FROM Months
    WHERE PeriodStart < CAST(? AS date)
), BuyerOrders AS (
    SELECT H.Production_year, H.Production_month,
           CASE
               WHEN N.NormalizedName LIKE '%GIBSON%'
                 OR N.NormalizedName LIKE 'GB %' THEN 'GIBSON'
               WHEN N.NormalizedName LIKE '%FENDER%' THEN 'FENDER'
               WHEN A.Account_Id IN (9841, 256, 236) THEN 'ESP'
               WHEN N.NormalizedName LIKE '%YAMAHA%' THEN 'YAMAHA'
               WHEN N.NormalizedName LIKE '%GUITAR CENTER%' THEN 'GUITAR CENTER'
               WHEN N.NormalizedName LIKE '%GUILD%' THEN 'GUILD'
               WHEN N.NormalizedName LIKE '%ZAGER%' THEN 'ZAGER'
               ELSE COALESCE(NULLIF(N.OriginalName, ''),
                    '(Unnamed account ' + CONVERT(varchar(20), A.Account_Id) + ')')
           END AS BuyerName,
           UPPER(LEFT(D.Item_Code, 2)) AS GuitarType,
           CAST(D.Qty AS decimal(19,4)) AS OrderQty,
           CAST(D.TotalPrice AS decimal(19,4)) AS OrderAmt
    FROM dbo.TAccSO_Header AS H
    INNER JOIN dbo.TAccSO_Detail AS D ON D.SO_Number = H.SO_Number
    INNER JOIN dbo.TAccount AS A ON A.Account_Id = H.Account_ID
    CROSS APPLY (SELECT LTRIM(RTRIM(A.Account_Name)) AS OriginalName,
                        UPPER(LTRIM(RTRIM(A.Account_Name))) AS NormalizedName) AS N
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
), Monthly AS (
    SELECT BuyerName, Production_year, Production_month,
           ISNULL(SUM(CASE WHEN GuitarType = 'AG' THEN OrderQty ELSE 0 END), 0) AS AG_OrderQty,
           ISNULL(SUM(CASE WHEN GuitarType = 'AG' THEN OrderAmt ELSE 0 END), 0) AS AG_OrderAmt,
           ISNULL(SUM(CASE WHEN GuitarType = 'EG' THEN OrderQty ELSE 0 END), 0) AS EG_OrderQty,
           ISNULL(SUM(CASE WHEN GuitarType = 'EG' THEN OrderAmt ELSE 0 END), 0) AS EG_OrderAmt
    FROM BuyerOrders
    GROUP BY BuyerName, Production_year, Production_month
)
SELECT BuyerName, Production_year, Production_month,
       AG_OrderQty, AG_OrderAmt, EG_OrderQty, EG_OrderAmt,
       CAST(AG_OrderQty AS decimal(28,4)) + CAST(EG_OrderQty AS decimal(28,4)) AS Total_OrderQty,
       CAST(AG_OrderAmt AS decimal(28,4)) + CAST(EG_OrderAmt AS decimal(28,4)) AS Total_OrderAmt
FROM Monthly
ORDER BY BuyerName, Production_year, Production_month
OPTION (MAXRECURSION 120);
