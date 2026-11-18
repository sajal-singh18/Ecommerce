USE EcommerceDB;

SELECT SUM(TotalAmount) AS TotalRevenue FROM Orders WHERE Status='Delivered';
SELECT COUNT(*) AS TotalOrders FROM Orders;
SELECT MONTH(OrderDate) AS Month, SUM(TotalAmount) AS Revenue FROM Orders WHERE Status='Delivered' GROUP BY MONTH(OrderDate) ORDER BY Month;
SELECT c.CustomerID,CONCAT(c.FirstName,' ',c.LastName) AS Customer, SUM(o.TotalAmount) AS TotalSpent 
FROM Customers c JOIN Orders o ON c.CustomerID=o.CustomerID 
GROUP BY c.CustomerID,c.FirstName,c.LastName 
ORDER BY TotalSpent DESC LIMIT 5;
SELECT p.ProductName,SUM(oi.Quantity*oi.UnitPrice) AS Revenue 
FROM Products p JOIN OrderItems oi ON p.ProductID=oi.ProductID 
GROUP BY p.ProductName ORDER BY Revenue DESC;
