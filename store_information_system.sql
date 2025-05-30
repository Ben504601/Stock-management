-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 10:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store_information_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerID` int(11) NOT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `MI` varchar(10) DEFAULT NULL,
  `phoneNo` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `deliveryID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `ETA` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `productID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`productID`, `name`, `description`) VALUES
(1, 'Dinorado Rice', 'Premium long-grain rice'),
(2, 'Jasmine Rice', 'Fragrant Thai jasmine rice'),
(3, 'Sinandomeng Rice', 'Soft local variety'),
(4, 'test', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoiceID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `issued_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_ordered`
--

CREATE TABLE `item_ordered` (
  `itemorderedID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `variationID` int(11) NOT NULL,
  `quantity_ordered` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_bank`
--

CREATE TABLE `online_bank` (
  `bankID` int(11) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `routingNo` varchar(50) DEFAULT NULL,
  `swift_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `date_ordered` datetime DEFAULT NULL,
  `total_items` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `pay_method` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `pay_method` int(11) NOT NULL,
  `bankID` int(11) DEFAULT NULL,
  `method_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_line`
--

CREATE TABLE `report_line` (
  `report_line` int(11) NOT NULL,
  `reportID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `variationID` int(11) NOT NULL,
  `total_quantity_sold` int(11) DEFAULT NULL,
  `total_sales_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_report`
--

CREATE TABLE `sales_report` (
  `reportID` int(11) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `month` tinyint(4) DEFAULT NULL,
  `generated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipment`
--

CREATE TABLE `shipment` (
  `shipmentID` int(11) NOT NULL,
  `supplierID` int(11) NOT NULL,
  `quantity_ordered` int(11) DEFAULT NULL,
  `total_Price` decimal(10,2) DEFAULT NULL,
  `date_Shipped` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `stockID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `variationID` int(11) NOT NULL,
  `quantity_on_hand` int(11) DEFAULT NULL,
  `reorder_level` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`stockID`, `productID`, `variationID`, `quantity_on_hand`, `reorder_level`) VALUES
(1, 1, 1, 100, 25),
(2, 1, 2, 0, 40),
(3, 1, 3, 115, 60),
(4, 2, 4, 50, 15),
(5, 2, 5, 90, 25),
(6, 2, 6, 35, 40),
(7, 3, 7, 20, 15),
(8, 3, 8, 60, 25),
(9, 3, 9, 90, 40),
(10, 3, 10, 50, 60),
(11, 4, 11, 0, 100);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplierID` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `phoneNo` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_brands`
--

CREATE TABLE `supplier_brands` (
  `supplierID` int(11) NOT NULL,
  `productID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variation`
--

CREATE TABLE `variation` (
  `variationID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `weight` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `variation`
--

INSERT INTO `variation` (`variationID`, `productID`, `weight`, `price`) VALUES
(1, 1, '25kg', 200.00),
(2, 1, '10kg', 80.00),
(3, 1, '5kg', 50.00),
(4, 2, '50kg', 220.00),
(5, 2, '25kg', 120.00),
(6, 2, '10kg', 60.00),
(7, 3, '50kg', 180.00),
(8, 3, '25kg', 100.00),
(9, 3, '10kg', 70.00),
(10, 3, '5kg', 40.00),
(11, 4, '50kg', 250.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerID`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`deliveryID`),
  ADD KEY `orderID` (`orderID`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`productID`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoiceID`),
  ADD KEY `orderID` (`orderID`);

--
-- Indexes for table `item_ordered`
--
ALTER TABLE `item_ordered`
  ADD PRIMARY KEY (`itemorderedID`),
  ADD KEY `orderID` (`orderID`),
  ADD KEY `productID` (`productID`),
  ADD KEY `variationID` (`variationID`);

--
-- Indexes for table `online_bank`
--
ALTER TABLE `online_bank`
  ADD PRIMARY KEY (`bankID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `customerID` (`customerID`),
  ADD KEY `pay_method` (`pay_method`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`pay_method`),
  ADD KEY `bankID` (`bankID`);

--
-- Indexes for table `report_line`
--
ALTER TABLE `report_line`
  ADD PRIMARY KEY (`report_line`),
  ADD KEY `reportID` (`reportID`),
  ADD KEY `productID` (`productID`),
  ADD KEY `variationID` (`variationID`);

--
-- Indexes for table `sales_report`
--
ALTER TABLE `sales_report`
  ADD PRIMARY KEY (`reportID`);

--
-- Indexes for table `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`shipmentID`),
  ADD KEY `supplierID` (`supplierID`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stockID`),
  ADD KEY `productID` (`productID`),
  ADD KEY `variationID` (`variationID`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplierID`);

--
-- Indexes for table `supplier_brands`
--
ALTER TABLE `supplier_brands`
  ADD PRIMARY KEY (`supplierID`,`productID`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `variation`
--
ALTER TABLE `variation`
  ADD PRIMARY KEY (`variationID`),
  ADD KEY `productID` (`productID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `deliveryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoiceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_ordered`
--
ALTER TABLE `item_ordered`
  MODIFY `itemorderedID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_bank`
--
ALTER TABLE `online_bank`
  MODIFY `bankID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `pay_method` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_line`
--
ALTER TABLE `report_line`
  MODIFY `report_line` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_report`
--
ALTER TABLE `sales_report`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipment`
--
ALTER TABLE `shipment`
  MODIFY `shipmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stockID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplierID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variation`
--
ALTER TABLE `variation`
  MODIFY `variationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`);

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`);

--
-- Constraints for table `item_ordered`
--
ALTER TABLE `item_ordered`
  ADD CONSTRAINT `item_ordered_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`),
  ADD CONSTRAINT `item_ordered_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `inventory` (`productID`),
  ADD CONSTRAINT `item_ordered_ibfk_3` FOREIGN KEY (`variationID`) REFERENCES `variation` (`variationID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`pay_method`) REFERENCES `payment_method` (`pay_method`);

--
-- Constraints for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD CONSTRAINT `payment_method_ibfk_1` FOREIGN KEY (`bankID`) REFERENCES `online_bank` (`bankID`);

--
-- Constraints for table `report_line`
--
ALTER TABLE `report_line`
  ADD CONSTRAINT `report_line_ibfk_1` FOREIGN KEY (`reportID`) REFERENCES `sales_report` (`reportID`),
  ADD CONSTRAINT `report_line_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `inventory` (`productID`),
  ADD CONSTRAINT `report_line_ibfk_3` FOREIGN KEY (`variationID`) REFERENCES `variation` (`variationID`);

--
-- Constraints for table `shipment`
--
ALTER TABLE `shipment`
  ADD CONSTRAINT `shipment_ibfk_1` FOREIGN KEY (`supplierID`) REFERENCES `supplier` (`supplierID`);

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`productID`) REFERENCES `inventory` (`productID`),
  ADD CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`variationID`) REFERENCES `variation` (`variationID`);

--
-- Constraints for table `supplier_brands`
--
ALTER TABLE `supplier_brands`
  ADD CONSTRAINT `supplier_brands_ibfk_1` FOREIGN KEY (`supplierID`) REFERENCES `supplier` (`supplierID`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_brands_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `inventory` (`productID`) ON DELETE CASCADE;

--
-- Constraints for table `variation`
--
ALTER TABLE `variation`
  ADD CONSTRAINT `variation_ibfk_1` FOREIGN KEY (`productID`) REFERENCES `inventory` (`productID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
