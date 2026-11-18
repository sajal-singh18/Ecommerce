USE EcommerceDB;

CREATE INDEX idx_customer_email
ON Customers(Email);

CREATE INDEX idx_product_name ON Products(ProductName);
CREATE INDEX idx_payment_status ON Payments(PaymentStatus);
CREATE INDEX idx_orderitem_product ON OrderItems(ProductID);
CREATE INDEX idx_order_status ON Orders(Status);
SHOW INDEX FROM Customers;
SHOW INDEX FROM Products;
SHOW INDEX FROM Payments;
