/* Q012: parameterized production period, SQL Server 2014 */
;WITH Months AS (
    SELECT CAST(? AS date) AS PeriodStart
    UNION ALL
    SELECT DATEADD(month, 1, PeriodStart)
    FROM Months WHERE PeriodStart < CAST(? AS date)
), OrderKeys AS (
    SELECT H.SO_Number, H.Production_year, H.Production_month,
           D.Item_Code, D.Dimension_ID,
           UPPER(LEFT(D.Item_Code, 2)) AS GuitarType,
           SUM(CAST(D.Qty AS decimal(19,4))) AS OrderQty,
           SUM(CAST(D.TotalPrice AS decimal(19,4))) AS OrderAmt,
           COUNT_BIG(*) AS DetailRows
    FROM dbo.TAccSO_Header AS H
    INNER JOIN dbo.TAccSO_Detail AS D ON D.SO_Number = H.SO_Number
    WHERE H.Production_month BETWEEN 1 AND 12
      AND EXISTS (SELECT 1 FROM Months AS C WHERE YEAR(C.PeriodStart) = H.Production_year AND MONTH(C.PeriodStart) = H.Production_month)
      AND H.Company_ID = 2
      AND H.ItemCategoryType = 'FG'
      AND ISNULL(H.IsNotActive, 0) = 0
      AND H.Approval_Status = 3
      AND H.isExport = 1
      AND H.Currency_ID = 'USD'
      AND UPPER(LTRIM(ISNULL(H.PO_NumCustomer, ''))) NOT LIKE 'SG%'
      AND UPPER(LTRIM(ISNULL(H.PO_NumCustomer, ''))) NOT LIKE 'GS%'
      AND (D.Item_Code LIKE 'AG%' OR D.Item_Code LIKE 'EG%')
      AND EXISTS (SELECT 1 FROM dbo.TAccount AS A WHERE A.Account_ID = H.Account_ID)
    GROUP BY H.SO_Number, H.Production_year, H.Production_month,
             D.Item_Code, D.Dimension_ID, UPPER(LEFT(D.Item_Code, 2))
), ShipmentKeys AS (
    SELECT S.SO_Number, S.Item_Code, S.Dimension_ID,
           SUM(CAST(S.Qty AS decimal(19,4))) AS DeliveredQty
    FROM dbo.TAccSN_Item AS S
    INNER JOIN dbo.TAccSN_Header AS SH ON SH.SN_Number = S.SN_Number
    WHERE ISNULL(SH.isVoid, 0) = 0 AND SH.Approval_Status = 3
      AND EXISTS (SELECT 1 FROM OrderKeys AS O WHERE O.SO_Number = S.SO_Number
        AND O.Item_Code = S.Item_Code
        AND (O.Dimension_ID = S.Dimension_ID OR (O.Dimension_ID IS NULL AND S.Dimension_ID IS NULL)))
    GROUP BY S.SO_Number, S.Item_Code, S.Dimension_ID
), Matched AS (
    SELECT O.*, ISNULL(S.DeliveredQty, 0) AS ExportQty
    FROM OrderKeys AS O
    LEFT JOIN ShipmentKeys AS S ON O.SO_Number = S.SO_Number
        AND O.Item_Code = S.Item_Code
        AND (O.Dimension_ID = S.Dimension_ID OR (O.Dimension_ID IS NULL AND S.Dimension_ID IS NULL))
), Valued AS (
    SELECT M.*,
           CAST(M.OrderQty AS decimal(28,4)) - CAST(M.ExportQty AS decimal(28,4)) AS BalanceQty,
           /* Provisional proportional allocation of booked order value. */
           CAST(CASE WHEN M.OrderQty = 0 THEN 0 ELSE
                CAST(M.OrderAmt AS decimal(19,4)) *
                CAST(CAST(M.ExportQty AS decimal(19,4)) / NULLIF(CAST(M.OrderQty AS decimal(19,4)), 0) AS decimal(19,10))
           END AS decimal(28,8)) AS ExportAmt
    FROM Matched AS M
), Monthly AS (
    SELECT Production_year, Production_month,
           SUM(CASE WHEN GuitarType='AG' THEN OrderQty ELSE 0 END) AS AG_OrderQty,
           SUM(CASE WHEN GuitarType='AG' THEN OrderAmt ELSE 0 END) AS AG_OrderAmt,
           SUM(CASE WHEN GuitarType='EG' THEN OrderQty ELSE 0 END) AS EG_OrderQty,
           SUM(CASE WHEN GuitarType='EG' THEN OrderAmt ELSE 0 END) AS EG_OrderAmt,
           SUM(CASE WHEN GuitarType='AG' THEN BalanceQty ELSE 0 END) AS AG_BalanceQty,
           SUM(CASE WHEN GuitarType='AG' THEN CAST(OrderAmt AS decimal(28,8))-ExportAmt ELSE 0 END) AS AG_BalanceAmt,
           SUM(CASE WHEN GuitarType='EG' THEN BalanceQty ELSE 0 END) AS EG_BalanceQty,
           SUM(CASE WHEN GuitarType='EG' THEN CAST(OrderAmt AS decimal(28,8))-ExportAmt ELSE 0 END) AS EG_BalanceAmt,
           SUM(CASE WHEN GuitarType='AG' THEN ExportQty ELSE 0 END) AS AG_ExportQty,
           SUM(CASE WHEN GuitarType='AG' THEN ExportAmt ELSE 0 END) AS AG_ExportAmt,
           SUM(CASE WHEN GuitarType='EG' THEN ExportQty ELSE 0 END) AS EG_ExportQty,
           SUM(CASE WHEN GuitarType='EG' THEN ExportAmt ELSE 0 END) AS EG_ExportAmt,
           SUM(CASE WHEN DetailRows > 1 THEN 1 ELSE 0 END) AS RepeatedKeys,
           SUM(CASE WHEN BalanceQty < 0 THEN 1 ELSE 0 END) AS NegativeBalanceKeys,
           SUM(CASE WHEN OrderQty = 0 THEN 1 ELSE 0 END) AS ZeroOrderQtyKeys,
           SUM(CASE WHEN Dimension_ID IS NULL THEN 1 ELSE 0 END) AS NullDimensionKeys,
           COUNT(DISTINCT SO_Number) AS SOCount
    FROM Valued
    GROUP BY Production_year, Production_month
)
SELECT YEAR(C.PeriodStart) AS Production_year, MONTH(C.PeriodStart) AS Production_month,
       ISNULL(M.AG_OrderQty,0) AS AG_OrderQty, ISNULL(M.AG_OrderAmt,0) AS AG_OrderAmt,
       ISNULL(M.EG_OrderQty,0) AS EG_OrderQty, ISNULL(M.EG_OrderAmt,0) AS EG_OrderAmt,
       ISNULL(M.AG_BalanceQty,0) AS AG_BalanceQty, ISNULL(M.AG_BalanceAmt,0) AS AG_BalanceAmt,
       ISNULL(M.EG_BalanceQty,0) AS EG_BalanceQty, ISNULL(M.EG_BalanceAmt,0) AS EG_BalanceAmt,
       ISNULL(M.AG_ExportQty,0) AS AG_ExportQty, ISNULL(M.AG_ExportAmt,0) AS AG_ExportAmt,
       ISNULL(M.EG_ExportQty,0) AS EG_ExportQty, ISNULL(M.EG_ExportAmt,0) AS EG_ExportAmt,
       ISNULL(M.RepeatedKeys,0) AS RepeatedKeys,
       ISNULL(M.NegativeBalanceKeys,0) AS NegativeBalanceKeys,
       ISNULL(M.ZeroOrderQtyKeys,0) AS ZeroOrderQtyKeys,
       ISNULL(M.NullDimensionKeys,0) AS NullDimensionKeys,
       ISNULL(M.SOCount,0) AS SOCount
FROM Months AS C
LEFT JOIN Monthly AS M ON M.Production_year = YEAR(C.PeriodStart)
    AND M.Production_month = MONTH(C.PeriodStart)
ORDER BY C.PeriodStart
OPTION (MAXRECURSION 120);
