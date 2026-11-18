DROP DATABASE IF EXISTS EcommerceDB;
CREATE DATABASE EcommerceDB;
USE EcommerceDB;

CREATE TABLE Customers(
CustomerID INT AUTO_INCREMENT PRIMARY KEY,
FirstName VARCHAR(50) NOT NULL,
LastName VARCHAR(50) NOT NULL,
Email VARCHAR(100) NOT NULL UNIQUE,
Phone VARCHAR(15) UNIQUE,
Address VARCHAR(255),
City VARCHAR(50),
State VARCHAR(50),
Country VARCHAR(50),
CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO Customers
(FirstName, LastName, Email, Phone, Address, City, State, Country)
VALUES
('Sundar','Yadav','sundar.yadav@gmail.com','9876543211','Sector 22','Chandigarh','Chandigarh','India'),
('Aryan','Singh','aryan.singh@gmail.com','9876543210','Model Town','Jalandhar','Punjab','India'),
('Sajal','Singh','sajal.singh@gmail.com','9876543212','Civil Lines','Lucknow','Uttar Pradesh','India'),
('Kashish','Sabharwal','kashish.sabharwal@gmail.com','9876543213','MG Road','Indore','Madhya Pradesh','India'),
('Neha','Patel','neha.patel@gmail.com','9876543214','Navrangpura','Ahmedabad','Gujarat','India'),
('Rohit','Yadav','rohit.yadav@gmail.com','9876543215','Rajendra Nagar','Patna','Bihar','India'),
('Sneha','Joshi','sneha.joshi@gmail.com','9876543216','FC Road','Pune','Maharashtra','India'),
('Karan','Malhotra','karan.malhotra@gmail.com','9876543217','Janakpuri','New Delhi','Delhi','India'),
('Anjali','Mehta','anjali.mehta@gmail.com','9876543218','Banjara Hills','Hyderabad','Telangana','India'),
('Vikas','Kumar','vikas.kumar@gmail.com','9876543219','Salt Lake','Kolkata','West Bengal','India'),
('Pooja','Saini','pooja.saini@gmail.com','9876543220','Vaishali Nagar','Jaipur','Rajasthan','India'),
('Deepak','Mishra','deepak.mishra@gmail.com','9876543221','Aliganj','Lucknow','Uttar Pradesh','India'),
('Nisha','Reddy','nisha.reddy@gmail.com','9876543222','Madhapur','Hyderabad','Telangana','India'),
('Harsh','Kapoor','harsh.kapoor@gmail.com','9876543223','Lajpat Nagar','New Delhi','Delhi','India'),
('Simran','Kaur','simran.kaur@gmail.com','9876543224','Ranjit Avenue','Amritsar','Punjab','India'),
('Aditya','Roy','aditya.roy@gmail.com','9876543225','Park Street','Kolkata','West Bengal','India'),
('Meera','Nair','meera.nair@gmail.com','9876543226','Kakkanad','Kochi','Kerala','India'),
('Sahil','Chopra','sahil.chopra@gmail.com','9876543227','Sector 15','Noida','Uttar Pradesh','India'),
('Kavya','Iyer','kavya.iyer@gmail.com','9876543228','T Nagar','Chennai','Tamil Nadu','India'),
('Arjun','Das','arjun.das@gmail.com','9876543229','Saheed Nagar','Bhubaneswar','Odisha','India');

CREATE TABLE Categories(
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO Categories (CategoryName)
VALUES
('Electronics'),
('Fashion'),
('Home & Kitchen'),
('Books'),
('Sports'),
('Beauty & Personal Care'),
('Toys & Games'),
('Grocery'),
('Furniture'),
('Automotive');

CREATE TABLE Suppliers(
SupplierID INT AUTO_INCREMENT PRIMARY KEY,
SupplierName VARCHAR(100) NOT NULL,
ContactPerson VARCHAR(100),
Email VARCHAR(100) UNIQUE,
Phone VARCHAR(15),
City VARCHAR(50),
Country VARCHAR(50)
);

INSERT INTO Suppliers
(SupplierName, ContactPerson, Email, Phone, City, Country)
VALUES
('TechWorld Pvt Ltd', 'Rajesh Kumar', 'contact@techworld.com', '9876501001', 'Bengaluru', 'India'),
('Fashion Hub', 'Anjali Sharma', 'info@fashionhub.com', '9876501002', 'Mumbai', 'India'),
('Home Essentials', 'Vikas Mehta', 'sales@homeessentials.com', '9876501003', 'Delhi', 'India'),
('Book Planet', 'Neha Verma', 'support@bookplanet.com', '9876501004', 'Kolkata', 'India'),
('Sports Zone', 'Rohit Singh', 'contact@sportszone.com', '9876501005', 'Chandigarh', 'India'),
('Beauty Mart', 'Priya Nair', 'sales@beautymart.com', '9876501006', 'Hyderabad', 'India'),
('Toy Universe', 'Sanjay Gupta', 'info@toyuniverse.com', '9876501007', 'Pune', 'India'),
('Fresh Grocery Suppliers', 'Deepak Yadav', 'orders@freshgrocery.com', '9876501008', 'Ahmedabad', 'India'),
('Furniture House', 'Karan Malhotra', 'support@furniturehouse.com', '9876501009', 'Jaipur', 'India'),
('AutoCare Distributors', 'Amit Patel', 'contact@autocare.com', '9876501010', 'Chennai', 'India');

CREATE TABLE Products(
ProductID INT AUTO_INCREMENT PRIMARY KEY,
ProductName VARCHAR(150) NOT NULL,
CategoryID INT NOT NULL,
SupplierID INT NOT NULL,
Price DECIMAL(10,2) NOT NULL CHECK (Price >= 0),
Stock INT NOT NULL DEFAULT 0 CHECK (Stock >= 0),
Description VARCHAR(255),
CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID),
FOREIGN KEY (SupplierID) REFERENCES Suppliers(SupplierID)
);

INSERT INTO Products
(ProductName, CategoryID, SupplierID, Price, Stock, Description)
VALUES
('Samsung Galaxy S24',1,1,74999.00,30,'5G Smartphone'),
('Apple iPhone 15',1,1,79999.00,25,'Latest Apple Smartphone'),
('Dell Inspiron 15',1,1,58999.00,18,'15-inch Laptop'),
('HP Pavilion Laptop',1,1,62999.00,15,'Intel i5 Laptop'),
('Boat Rockerz 550',1,1,2499.00,60,'Wireless Headphones'),
('Men Casual T-Shirt',2,2,799.00,120,'100% Cotton T-Shirt'),
('Women Denim Jacket',2,2,2499.00,45,'Blue Denim Jacket'),
('Running Shoes',2,2,3499.00,70,'Lightweight Sports Shoes'),
('Leather Wallet',2,2,999.00,80,'Genuine Leather Wallet'),
('Sports Cap',2,2,499.00,90,'Adjustable Cotton Cap'),
('Mixer Grinder',3,3,3999.00,40,'750W Mixer Grinder'),
('Microwave Oven',3,3,8999.00,20,'20L Microwave'),
('Electric Kettle',3,3,1499.00,55,'1.5L Electric Kettle'),
('Dinner Set',3,3,2999.00,35,'24 Piece Dinner Set'),
('Pressure Cooker',3,3,1899.00,45,'5L Stainless Steel'),
('Atomic Habits',4,4,499.00,100,'Book by James Clear'),
('Rich Dad Poor Dad',4,4,399.00,90,'Finance Book'),
('The Alchemist',4,4,350.00,110,'Novel'),
('Think and Grow Rich',4,4,450.00,80,'Motivational Book'),
('Deep Work',4,4,550.00,60,'Productivity Book'),
('Cricket Bat',5,5,3499.00,35,'English Willow Bat'),
('Football',5,5,899.00,75,'FIFA Standard Football'),
('Badminton Racket',5,5,1999.00,40,'Professional Racket'),
('Yoga Mat',5,5,799.00,85,'Anti-slip Yoga Mat'),
('Gym Gloves',5,5,599.00,120,'Workout Gloves'),
('Face Wash',6,6,299.00,150,'Oil Control Face Wash'),
('Shampoo',6,6,450.00,90,'Herbal Shampoo'),
('Body Lotion',6,6,399.00,85,'Moisturizing Lotion'),
('Perfume',6,6,1499.00,40,'Long Lasting Perfume'),
('Lipstick',6,6,699.00,70,'Matte Finish'),
('Remote Control Car',7,7,1999.00,30,'Rechargeable RC Car'),
('Building Blocks',7,7,1499.00,45,'Creative Toy Set'),
('Puzzle Game',7,7,599.00,80,'1000 Piece Puzzle'),
('Teddy Bear',7,7,899.00,55,'Soft Toy'),
('Chess Board',7,7,799.00,40,'Wooden Chess Set'),
('Basmati Rice 5kg',8,8,699.00,70,'Premium Rice'),
('Sunflower Oil 1L',8,8,199.00,120,'Cooking Oil'),
('Green Tea',8,8,299.00,95,'Organic Green Tea'),
('Coffee Powder',8,8,499.00,75,'Instant Coffee'),
('Dry Fruits Mix',8,8,899.00,50,'Healthy Snack'),
('Office Chair',9,9,5999.00,18,'Ergonomic Chair'),
('Study Table',9,9,7499.00,15,'Wooden Study Table'),
('Wooden Wardrobe',9,9,15999.00,8,'3 Door Wardrobe'),
('Sofa Set',9,9,25999.00,6,'5 Seater Sofa'),
('Bookshelf',9,9,4499.00,20,'Wooden Bookshelf'),
('Car Vacuum Cleaner',10,10,2499.00,30,'Portable Vacuum'),
('Bike Helmet',10,10,1999.00,45,'ISI Certified Helmet'),
('Car Phone Holder',10,10,499.00,90,'Dashboard Mount'),
('Tyre Inflator',10,10,2999.00,25,'Digital Air Pump'),
('Car Cleaning Kit',10,10,1499.00,35,'Complete Cleaning Kit');

CREATE TABLE Orders(
OrderID INT AUTO_INCREMENT PRIMARY KEY,
CustomerID INT NOT NULL,
OrderDate DATETIME DEFAULT CURRENT_TIMESTAMP,
Status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
TotalAmount DECIMAL(10,2) DEFAULT 0.00,
FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID)
);

INSERT INTO Orders
(CustomerID, OrderDate, Status)
VALUES
(1,'2026-01-05 10:15:00','Delivered'),
(2,'2026-01-06 11:20:00','Delivered'),
(3,'2026-01-07 09:45:00','Delivered'),
(4,'2026-01-08 14:30:00','Shipped'),
(5,'2026-01-09 16:00:00','Delivered'),
(6,'2026-01-10 13:10:00','Processing'),
(7,'2026-01-11 17:45:00','Delivered'),
(8,'2026-01-12 10:00:00','Cancelled'),
(9,'2026-01-13 12:30:00','Delivered'),
(10,'2026-01-14 15:15:00','Pending'),
(11,'2026-01-15 11:00:00','Delivered'),
(12,'2026-01-16 09:20:00','Processing'),
(13,'2026-01-17 18:30:00','Delivered'),
(14,'2026-01-18 10:50:00','Delivered'),
(15,'2026-01-19 13:40:00','Shipped'),
(16,'2026-01-20 15:00:00','Delivered'),
(17,'2026-01-21 16:20:00','Pending'),
(18,'2026-01-22 11:45:00','Delivered'),
(19,'2026-01-23 14:25:00','Processing'),
(20,'2026-01-24 09:10:00','Delivered'),
(1,'2026-02-01 10:15:00','Delivered'),
(2,'2026-02-02 12:30:00','Pending'),
(3,'2026-02-03 13:45:00','Delivered'),
(4,'2026-02-04 09:15:00','Delivered'),
(5,'2026-02-05 17:20:00','Processing'),
(6,'2026-02-06 11:30:00','Delivered'),
(7,'2026-02-07 15:00:00','Delivered'),
(8,'2026-02-08 10:40:00','Cancelled'),
(9,'2026-02-09 14:10:00','Delivered'),
(10,'2026-02-10 16:30:00','Shipped'),
(11,'2026-02-11 11:10:00','Delivered'),
(12,'2026-02-12 09:50:00','Delivered'),
(13,'2026-02-13 13:20:00','Pending'),
(14,'2026-02-14 17:40:00','Delivered'),
(15,'2026-02-15 15:30:00','Processing'),
(16,'2026-02-16 12:00:00','Delivered'),
(17,'2026-02-17 10:25:00','Delivered'),
(18,'2026-02-18 14:50:00','Shipped'),
(19,'2026-02-19 16:45:00','Delivered'),
(20,'2026-02-20 09:30:00','Pending');

CREATE TABLE OrderItems(
OrderItemID INT AUTO_INCREMENT PRIMARY KEY,
OrderID INT NOT NULL,
ProductID INT NOT NULL,
Quantity INT NOT NULL CHECK (Quantity > 0),
UnitPrice DECIMAL(10,2) NOT NULL,
FOREIGN KEY (OrderID) REFERENCES Orders(OrderID),
FOREIGN KEY (ProductID) REFERENCES Products(ProductID)
);

INSERT INTO OrderItems
(OrderID, ProductID, Quantity, UnitPrice)
VALUES
(1,1,1,74999.00),
(1,5,1,2499.00),
(2,3,1,58999.00),
(3,6,2,799.00),
(3,9,1,999.00),
(4,12,1,8999.00),
(5,16,1,499.00),
(6,8,1,3499.00),
(7,36,1,699.00),
(8,31,1,1999.00),
(9,42,1,7499.00),
(10,2,1,79999.00),
(11,43,1,15999.00),
(12,32,1,1499.00),
(13,19,1,450.00),
(14,11,1,3999.00),
(15,22,1,899.00),
(16,44,1,25999.00),
(17,2,1,79999.00),
(18,6,1,799.00),
(19,32,1,1499.00),
(20,37,1,699.00),
(21,23,1,1999.00),
(22,41,1,5999.00),
(23,24,1,799.00),
(24,18,1,350.00),
(25,33,1,1499.00),
(26,45,1,4499.00),
(27,46,1,2499.00),
(28,48,1,499.00),
(29,15,1,1899.00),
(30,22,1,899.00),
(31,5,1,2499.00),
(32,10,1,499.00),
(32,24,1,799.00),
(33,42,1,7499.00),
(34,9,1,999.00),
(35,2,1,79999.00),
(36,14,1,2999.00),
(37,20,1,550.00),
(38,38,1,299.00),
(39,47,1,1999.00),
(40,1,1,74999.00),
(4,13,1,1499.00),
(6,25,2,599.00),
(9,17,1,399.00),
(11,21,1,3499.00),
(14,27,1,450.00),
(16,35,1,799.00),
(18,29,1,1499.00),
(20,39,1,499.00),
(22,50,1,1499.00),
(24,40,1,899.00),
(26,7,1,2499.00),
(28,34,1,899.00),
(30,30,1,699.00),
(33,49,1,2999.00),
(36,26,1,299.00),
(39,4,1,62999.00);

CREATE TABLE Payments(
PaymentID INT AUTO_INCREMENT PRIMARY KEY,
OrderID INT NOT NULL,
PaymentMethod VARCHAR(20)
CHECK
	(
		PaymentMethod IN
		(
			'Credit Card',
			'Debit Card',
			'UPI',
			'Net Banking',
			'Cash on Delivery'
		)
	),
PaymentStatus VARCHAR(20) DEFAULT 'Pending'
CHECK
(
		PaymentStatus IN
	(
			'Pending',
			'Completed',
			'Failed',
			'Refunded'
	)
),

Amount DECIMAL(10,2) CHECK(Amount >= 0),
PaymentDate DATETIME DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY(OrderID) REFERENCES Orders(OrderID)
);

INSERT INTO Payments
(OrderID, PaymentMethod, PaymentStatus, Amount, PaymentDate)
VALUES
(1,'Credit Card','Completed',77498.00,'2026-01-05 10:30:00'),
(2,'UPI','Completed',58999.00,'2026-01-06 11:35:00'),
(3,'Debit Card','Completed',2597.00,'2026-01-07 10:00:00'),
(4,'Net Banking','Completed',10498.00,'2026-01-08 14:45:00'),
(5,'Cash on Delivery','Completed',499.00,'2026-01-09 16:20:00'),
(6,'UPI','Pending',4697.00,'2026-01-10 13:30:00'),
(7,'Credit Card','Completed',199.00,'2026-01-11 18:00:00'),
(8,'UPI','Refunded',1999.00,'2026-01-12 10:15:00'),
(9,'Debit Card','Completed',7898.00,'2026-01-13 12:45:00'),
(10,'Cash on Delivery','Pending',2499.00,'2026-01-14 15:30:00'),
(11,'Net Banking','Completed',19498.00,'2026-01-15 11:20:00'),
(12,'UPI','Pending',1499.00,'2026-01-16 09:35:00'),
(13,'Credit Card','Completed',450.00,'2026-01-17 18:45:00'),
(14,'Debit Card','Completed',4449.00,'2026-01-18 11:05:00'),
(15,'Cash on Delivery','Completed',899.00,'2026-01-19 14:00:00'),
(16,'Credit Card','Completed',26798.00,'2026-01-20 15:20:00'),
(17,'Net Banking','Pending',79999.00,'2026-01-21 16:40:00'),
(18,'UPI','Completed',2298.00,'2026-01-22 12:00:00'),
(19,'Debit Card','Pending',1499.00,'2026-01-23 14:40:00'),
(20,'Credit Card','Completed',1198.00,'2026-01-24 09:25:00'),
(21,'UPI','Completed',1999.00,'2026-02-01 10:30:00'),
(22,'Net Banking','Pending',7498.00,'2026-02-02 12:45:00'),
(23,'Debit Card','Completed',799.00,'2026-02-03 14:00:00'),
(24,'Credit Card','Completed',1249.00,'2026-02-04 09:30:00'),
(25,'Cash on Delivery','Pending',1499.00,'2026-02-05 17:35:00'),
(26,'UPI','Completed',6998.00,'2026-02-06 11:45:00'),
(27,'Credit Card','Completed',2499.00,'2026-02-07 15:20:00'),
(28,'UPI','Refunded',1398.00,'2026-02-08 10:55:00'),
(29,'Debit Card','Completed',1899.00,'2026-02-09 14:25:00'),
(30,'Cash on Delivery','Completed',1598.00,'2026-02-10 16:45:00'),
(31,'Credit Card','Completed',2499.00,'2026-02-11 11:25:00'),
(32,'UPI','Completed',1298.00,'2026-02-12 10:05:00'),
(33,'Net Banking','Pending',10498.00,'2026-02-13 13:40:00'),
(34,'Debit Card','Completed',999.00,'2026-02-14 18:00:00'),
(35,'Credit Card','Pending',79999.00,'2026-02-15 15:45:00'),
(36,'UPI','Completed',3298.00,'2026-02-16 12:15:00'),
(37,'Debit Card','Completed',550.00,'2026-02-17 10:40:00'),
(38,'Cash on Delivery','Completed',299.00,'2026-02-18 15:05:00'),
(39,'Credit Card','Completed',64998.00,'2026-02-19 17:00:00'),
(40,'Net Banking','Pending',74999.00,'2026-02-20 09:45:00');

CREATE TABLE Inventory
(
InventoryID INT AUTO_INCREMENT PRIMARY KEY,
ProductID INT NOT NULL UNIQUE,
AvailableStock INT
	DEFAULT 0
	CHECK(AvailableStock >= 0),
Warehouse VARCHAR(100),
LastUpdated TIMESTAMP
	DEFAULT CURRENT_TIMESTAMP
	ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY(ProductID) REFERENCES Products(ProductID)
);

INSERT INTO Inventory
(ProductID, AvailableStock, Warehouse)
VALUES
(1,30,'Delhi Warehouse'),
(2,25,'Delhi Warehouse'),
(3,18,'Mumbai Warehouse'),
(4,15,'Mumbai Warehouse'),
(5,60,'Delhi Warehouse'),
(6,120,'Bengaluru Warehouse'),
(7,45,'Bengaluru Warehouse'),
(8,70,'Chennai Warehouse'),
(9,80,'Delhi Warehouse'),
(10,90,'Mumbai Warehouse'),
(11,40,'Hyderabad Warehouse'),
(12,20,'Hyderabad Warehouse'),
(13,55,'Delhi Warehouse'),
(14,35,'Mumbai Warehouse'),
(15,45,'Chennai Warehouse'),
(16,100,'Kolkata Warehouse'),
(17,90,'Kolkata Warehouse'),
(18,110,'Delhi Warehouse'),
(19,80,'Mumbai Warehouse'),
(20,60,'Hyderabad Warehouse'),
(21,35,'Delhi Warehouse'),
(22,75,'Delhi Warehouse'),
(23,40,'Mumbai Warehouse'),
(24,85,'Bengaluru Warehouse'),
(25,120,'Chennai Warehouse'),
(26,150,'Delhi Warehouse'),
(27,90,'Mumbai Warehouse'),
(28,85,'Hyderabad Warehouse'),
(29,40,'Delhi Warehouse'),
(30,70,'Kolkata Warehouse'),
(31,30,'Bengaluru Warehouse'),
(32,45,'Delhi Warehouse'),
(33,80,'Mumbai Warehouse'),
(34,55,'Hyderabad Warehouse'),
(35,40,'Delhi Warehouse'),
(36,70,'Kolkata Warehouse'),
(37,120,'Mumbai Warehouse'),
(38,95,'Delhi Warehouse'),
(39,75,'Chennai Warehouse'),
(40,50,'Hyderabad Warehouse'),
(41,18,'Delhi Warehouse'),
(42,15,'Mumbai Warehouse'),
(43,8,'Bengaluru Warehouse'),
(44,6,'Hyderabad Warehouse'),
(45,20,'Delhi Warehouse'),
(46,30,'Chennai Warehouse'),
(47,45,'Mumbai Warehouse'),
(48,90,'Delhi Warehouse'),
(49,25,'Hyderabad Warehouse'),
(50,35,'Bengaluru Warehouse');