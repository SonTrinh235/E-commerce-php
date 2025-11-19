-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: E-commerce-php
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `buyers`
--

DROP TABLE IF EXISTS `buyers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buyers` (
  `BUYERID` varchar(10) NOT NULL,
  `MEMBERSHIP_LEVEL` varchar(10) NOT NULL,
  `REWARD_POINTS` int DEFAULT NULL,
  UNIQUE KEY `BUYERID` (`BUYERID`),
  CONSTRAINT `buyers_ibfk_1` FOREIGN KEY (`BUYERID`) REFERENCES `users` (`USERID`),
  CONSTRAINT `CHK_REWARD_POINTS` CHECK ((`REWARD_POINTS` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buyers`
--

LOCK TABLES `buyers` WRITE;
/*!40000 ALTER TABLE `buyers` DISABLE KEYS */;
INSERT INTO `buyers` VALUES ('B_1','VIP',100),('B_2','NORMAL',10),('B_3','VIP',120),('B_4','VIP',150),('B_5','NORMAL',0);
/*!40000 ALTER TABLE `buyers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `CARTID` varchar(10) NOT NULL,
  `CART_TIME` time NOT NULL,
  `CART_DATE` date NOT NULL,
  `CART_QUANTITY` int NOT NULL,
  `BUYERID` varchar(10) NOT NULL,
  PRIMARY KEY (`CARTID`),
  KEY `BUYERID` (`BUYERID`),
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`BUYERID`) REFERENCES `buyers` (`BUYERID`),
  CONSTRAINT `CHK_CART_QUANTITY` CHECK ((`CART_QUANTITY` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES ('CART_1','23:07:07','2025-11-12',2,'B_1'),('CART_2','23:08:26','2025-11-12',2,'B_2'),('CART_3','23:09:43','2025-11-12',5,'B_3'),('CART_4','23:10:58','2025-11-12',9,'B_4'),('CART_5','23:10:58','2025-11-12',1,'B_5');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `CAT_NAME` varchar(50) NOT NULL,
  `PARENTCAT_NAME` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`CAT_NAME`),
  KEY `PARENTCAT_NAME` (`PARENTCAT_NAME`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`PARENTCAT_NAME`) REFERENCES `categories` (`CAT_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES ('Đồng hồ',NULL),('Thiết bị điện tử',NULL),('Thời trang nam',NULL),('Đồng hồ nam','Đồng hồ'),('Loa','Thiết bị điện tử'),('Phụ kiện tivi','Thiết bị điện tử'),('Áo Hoodie','Thời trang nam'),('Áo khoác','Thời trang nam');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confirms`
--

DROP TABLE IF EXISTS `confirms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `confirms` (
  `PRODUCTID` varchar(50) NOT NULL,
  `ORDERID` varchar(10) NOT NULL,
  `SELLERID` varchar(10) NOT NULL,
  `CONF_TIME` time NOT NULL,
  `CONF_DATE` date NOT NULL,
  `CONF_STATUS` varchar(20) NOT NULL,
  KEY `PRODUCTID` (`PRODUCTID`),
  KEY `ORDERID` (`ORDERID`),
  KEY `SELLERID` (`SELLERID`),
  CONSTRAINT `confirms_ibfk_1` FOREIGN KEY (`PRODUCTID`) REFERENCES `products` (`PRODUCTID`),
  CONSTRAINT `confirms_ibfk_2` FOREIGN KEY (`ORDERID`) REFERENCES `orders` (`ORDERID`),
  CONSTRAINT `confirms_ibfk_3` FOREIGN KEY (`SELLERID`) REFERENCES `sellers` (`SELLERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confirms`
--

LOCK TABLES `confirms` WRITE;
/*!40000 ALTER TABLE `confirms` DISABLE KEYS */;
/*!40000 ALTER TABLE `confirms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `ORDERID` varchar(10) NOT NULL,
  `BUYERID` varchar(10) NOT NULL,
  `ORD_QUANTITY` int NOT NULL,
  `ORD_PRICE` int NOT NULL,
  `SHIP_TIME` time NOT NULL,
  `SHIP_DATE` date NOT NULL,
  PRIMARY KEY (`ORDERID`),
  KEY `BUYERID` (`BUYERID`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`BUYERID`) REFERENCES `buyers` (`BUYERID`),
  CONSTRAINT `CHK_ORD_PRICE` CHECK ((`ORD_PRICE` > 0)),
  CONSTRAINT `CHK_ORD_QUANTITY` CHECK ((`ORD_QUANTITY` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES ('ORD_1','B_1',2,96000,'11:37:23','2025-11-20'),('ORD_2','B_1',1,96000,'11:37:23','2025-11-16'),('ORD_3','B_5',5,150000,'11:37:23','2025-11-23'),('ORD_4','B_4',4,80000,'11:37:23','2025-11-18'),('ORD_5','B_2',2,36000,'11:37:23','2025-11-19');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `ORDERID` varchar(10) NOT NULL,
  `PAY_NUMBER` varchar(10) NOT NULL,
  `PAY_TIME` time NOT NULL,
  `PAY_DATE` date NOT NULL,
  `PAY_METHOD` varchar(20) NOT NULL,
  `STATUS_OF_ORDER` varchar(20) NOT NULL,
  PRIMARY KEY (`PAY_NUMBER`),
  KEY `ORDERID` (`ORDERID`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`ORDERID`) REFERENCES `orders` (`ORDERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES ('ORD_1','PAY_1','11:53:12','2025-11-20','Tiền mặt','Chưa thanh toán'),('ORD_1','PAY_2','11:53:12','2025-11-16','Chuyển khoản','Đã thanh toán'),('ORD_2','PAY_3','11:53:12','2025-11-23','Chuyển khoản','Đã thanh toán'),('ORD_4','PAY_4','11:53:12','2025-11-18','Tiền mặt','Chưa thanh toán'),('ORD_3','PAY_5','11:53:12','2025-11-19','Tiền mặt','Chưa thanh toán'),('ORD_2','PAY_6','21:41:33','2025-11-13','Tiền mặt','Chưa thanh toán');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `places`
--

DROP TABLE IF EXISTS `places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `places` (
  `BUYERID` varchar(10) NOT NULL,
  `PRODUCTID` varchar(50) NOT NULL,
  KEY `BUYERID` (`BUYERID`),
  KEY `places_ibfk_2` (`PRODUCTID`),
  CONSTRAINT `places_ibfk_1` FOREIGN KEY (`BUYERID`) REFERENCES `buyers` (`BUYERID`),
  CONSTRAINT `places_ibfk_2` FOREIGN KEY (`PRODUCTID`) REFERENCES `products` (`PRODUCTID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `places`
--

LOCK TABLES `places` WRITE;
/*!40000 ALTER TABLE `places` DISABLE KEYS */;
INSERT INTO `places` VALUES ('B_1','PRO_3'),('B_2','PRO_5'),('B_4','PRO_5'),('B_1','PRO_1');
/*!40000 ALTER TABLE `places` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `PRODUCTID` varchar(50) NOT NULL,
  `PRO_NAME` varchar(50) DEFAULT NULL,
  `PRO_DESCRIPTION` varchar(1000) DEFAULT NULL,
  `PRO_PRICE` int NOT NULL,
  `SELLERID` varchar(10) NOT NULL,
  `CAT_NAME` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`PRODUCTID`),
  KEY `SELLERID` (`SELLERID`),
  KEY `CAT_NAME` (`CAT_NAME`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`SELLERID`) REFERENCES `sellers` (`SELLERID`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`CAT_NAME`) REFERENCES `categories` (`CAT_NAME`),
  CONSTRAINT `CHK_PRO_PRICE` CHECK ((`PRO_PRICE` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES ('PRO_1','Điều khiển TV LG','Nguồn: 2 viên pin AAA, Khoảng cách sử dụng: 8m, Kích thước: 235x69x12mm, Trọng lượng: 68g',32000,'S_3','Phụ kiện tivi'),('PRO_2','Loa Bluetooth FLIP 8','Thời gian sạc pin: 3 giờ, Thời gian chơi nhạc: lên đến 10 giờ, Kích thước: 181x69x74mm, Trọng lượng: 540g',323000,'S_3','Loa'),('PRO_3','Áo khoác Bomber','Hướng dẫn rửa: Có thể giặt máy, Chất liệu: vật liệu dù hai lớp chất lượng, Size: M-L-XL-XXL',139900,'S_1','Áo khoác'),('PRO_5','Đồng hồ nam Orlado','Chất liệu vỏ: Thép không rỉ, Số kim: 3 kim, Năng lượng sử dụng: Pin',75000,'S_4','Đồng hồ nam');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receives`
--

DROP TABLE IF EXISTS `receives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receives` (
  `BUYERID` varchar(10) DEFAULT NULL,
  `SELLERID` varchar(10) DEFAULT NULL,
  `VOUCHER` varchar(50) DEFAULT NULL,
  `DISCOUNT` varchar(50) DEFAULT NULL,
  KEY `BUYERID` (`BUYERID`),
  KEY `SELLERID` (`SELLERID`),
  CONSTRAINT `receives_ibfk_1` FOREIGN KEY (`BUYERID`) REFERENCES `buyers` (`BUYERID`),
  CONSTRAINT `receives_ibfk_2` FOREIGN KEY (`SELLERID`) REFERENCES `sellers` (`SELLERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receives`
--

LOCK TABLES `receives` WRITE;
/*!40000 ALTER TABLE `receives` DISABLE KEYS */;
INSERT INTO `receives` VALUES ('B_1','S_1','FREE SHIP','Giảm 10% cho lần mua sau'),('B_1','S_2','Tặng vé xem phim','Giảm 10% cho lần mua sau'),('B_3','S_5','FREE SHIP','Giảm 20% cho lần mua sau'),('B_2','S_3','Tặng card điện thoại','Giảm 50% cho lần mua sau'),('B_5','S_4','FREE SHIP','Giảm 10% cho lần mua sau');
/*!40000 ALTER TABLE `receives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `BUYERID` varchar(10) NOT NULL,
  `PRODUCTID` varchar(50) NOT NULL,
  `REV_TEXT` varchar(1000) DEFAULT NULL,
  `REV_RATING` int DEFAULT NULL,
  KEY `BUYERID` (`BUYERID`),
  KEY `reviews_ibfk_2` (`PRODUCTID`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`BUYERID`) REFERENCES `buyers` (`BUYERID`),
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`PRODUCTID`) REFERENCES `products` (`PRODUCTID`) ON DELETE CASCADE,
  CONSTRAINT `CHK_REV_RATING` CHECK (((`REV_RATING` >= 0) and (`REV_RATING` <= 5)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES ('B_1','PRO_2','Sản phẩm chất lượng tốt',4),('B_3','PRO_3','Sản phẩm bị hư hỏng',2),('B_2','PRO_2','Sản phẩm chất lượng tốt',5),('B_5','PRO_1','Sản phẩm được giao trễ',2);
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sellers`
--

DROP TABLE IF EXISTS `sellers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sellers` (
  `SELLERID` varchar(10) NOT NULL,
  `SHOP_NAME` varchar(20) NOT NULL,
  `SHOP_RATING` int DEFAULT NULL,
  UNIQUE KEY `SELLERID` (`SELLERID`),
  CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`SELLERID`) REFERENCES `users` (`USERID`),
  CONSTRAINT `CHK_SHOP_RATING` CHECK (((`SHOP_RATING` >= 0) and (`SHOP_RATING` <= 5)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sellers`
--

LOCK TABLES `sellers` WRITE;
/*!40000 ALTER TABLE `sellers` DISABLE KEYS */;
INSERT INTO `sellers` VALUES ('S_1','Bag King',3),('S_2','Manxury',4),('S_3','LOVITO',2),('S_4','ELLEHA',5),('S_5','TOPICK',5);
/*!40000 ALTER TABLE `sellers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores` (
  `CARTID` varchar(10) NOT NULL,
  `PRODUCTID` varchar(50) NOT NULL,
  KEY `CARTID` (`CARTID`),
  KEY `stores_ibfk_2` (`PRODUCTID`),
  CONSTRAINT `stores_ibfk_1` FOREIGN KEY (`CARTID`) REFERENCES `carts` (`CARTID`),
  CONSTRAINT `stores_ibfk_2` FOREIGN KEY (`PRODUCTID`) REFERENCES `products` (`PRODUCTID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stores`
--

LOCK TABLES `stores` WRITE;
/*!40000 ALTER TABLE `stores` DISABLE KEYS */;
INSERT INTO `stores` VALUES ('CART_1','PRO_1'),('CART_1','PRO_2'),('CART_2','PRO_3'),('CART_3','PRO_2');
/*!40000 ALTER TABLE `stores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `updates`
--

DROP TABLE IF EXISTS `updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `updates` (
  `PRODUCTID` varchar(50) NOT NULL,
  `SELLERID` varchar(10) NOT NULL,
  `PRO_QUANTITY` int DEFAULT NULL,
  `PRO_STATUS` varchar(25) DEFAULT NULL,
  KEY `SELLERID` (`SELLERID`),
  KEY `updates_ibfk_1` (`PRODUCTID`),
  CONSTRAINT `updates_ibfk_1` FOREIGN KEY (`PRODUCTID`) REFERENCES `products` (`PRODUCTID`) ON DELETE CASCADE,
  CONSTRAINT `updates_ibfk_2` FOREIGN KEY (`SELLERID`) REFERENCES `sellers` (`SELLERID`),
  CONSTRAINT `CHK_PRO_QUANTITY` CHECK ((`PRO_QUANTITY` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `updates`
--

LOCK TABLES `updates` WRITE;
/*!40000 ALTER TABLE `updates` DISABLE KEYS */;
INSERT INTO `updates` VALUES ('PRO_1','S_3',200,'Còn hàng'),('PRO_2','S_3',50,'Còn hàng'),('PRO_3','S_1',20,'Còn hàng'),('PRO_5','S_4',30,'Còn hàng');
/*!40000 ALTER TABLE `updates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_emails`
--

DROP TABLE IF EXISTS `user_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_emails` (
  `USERID` varchar(10) NOT NULL,
  `EMAIL` varchar(25) NOT NULL,
  KEY `USERID` (`USERID`),
  CONSTRAINT `user_emails_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `users` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_emails`
--

LOCK TABLES `user_emails` WRITE;
/*!40000 ALTER TABLE `user_emails` DISABLE KEYS */;
INSERT INTO `user_emails` VALUES ('B_1','user001@gmail.com.vn'),('B_2','user002@gmail.com.vn'),('B_3','user003@gmail.com.vn'),('B_4','user004@gmail.com.vn'),('B_5','user005@gmail.com.vn'),('S_1','user006@gmail.com.vn'),('S_2','user007@gmail.com.vn'),('S_3','user008@gmail.com.vn'),('S_4','user009@gmail.com.vn'),('S_5','user010@gmail.com.vn');
/*!40000 ALTER TABLE `user_emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `USERID` varchar(10) NOT NULL,
  `SSN` varchar(20) DEFAULT NULL,
  `USERNAME` varchar(15) NOT NULL,
  `USER_PASSWORD` varchar(15) NOT NULL,
  `FIRSTNAME` varchar(30) NOT NULL,
  `LASTNAME` varchar(10) NOT NULL,
  `ADDRESS` varchar(50) NOT NULL,
  `PHONE` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`USERID`),
  CONSTRAINT `CHK_PHONE` CHECK (regexp_like(`PHONE`,_utf8mb4'^0[0-9]{9}$'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('B_1','072201002571','ABC1','abc1','Nguyễn Văn','Tèo','Phường Long Hoa, Huyện Hòa Thành, Tây Ninh','0912536487'),('B_2','062205601465','ABC2','abc2','Nguyễn Văn','Hùng','Phường 10, Quận Tân Bình, TPHCM','0358536087'),('B_3','073605601465','ABC5','abc5','Lê Thị','Bảy','Phường 7, Quận Tân Bình, TPHCM','0958535428'),('B_4','079555605682','ABC6','abc6','Lý Hoàng','Sơn','Xã Nhơn Đức, Huyện Nhà Bè, TPHCM','0962784987'),('B_5','078882605352','ABC7','abc7','Nguyễn Hoàng','Nghĩa','Xã Bình Khánh, Huyện Cần Giờ, TPHCM','0345255587'),('B_6','072201332572','XYZ1','abc12','Nguyễn Tấn','Lực','Phường Long Hoa, Huyện Hòa Thành, Tây Ninh','0912538536'),('S_1','065205609875','ABC3','abc3','Trần Văn','Cường','Phường 1, Quận Gò Vấp, TPHCM','0332675087'),('S_2','078532605682','ABC4','abc4','Lý Quốc','Dũng','Phường 4, Quận Gò Vấp, TPHCM','0962655587'),('S_3','074432605111','ABC8','abc8','Nguyễn Thanh','Phong','Phường 8, Quận Gò Vấp, TPHCM','0962666887'),('S_4','063332605234','ABC9','abc9','Đậu Đức','Nghĩa','Phường 9, Quận Tân Bình, TPHCM','0932489678'),('S_5','067732605789','ABC10','abc10','Trần Quốc','Toàn','Phường 1, Quận Gò Vấp, TPHCM','0368713528');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `USER_ACCOUNT` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    DECLARE v_COUNT INT;
    -- USERNAME chỉ được chứa chữ và số
    IF NEW.USERNAME NOT REGEXP '^[A-Za-z0-9]+$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Lỗi: USERNAME chỉ được phép chứa chữ cái và số, không chứa ký tự đặc biệt!';
    END IF;
    -- PASSWORD phải có ít nhất 1 chữ và 1 số, dài tối thiểu 5 ký tự
    IF NEW.USER_PASSWORD NOT REGEXP '^(?=.*[A-Za-z])(?=.*[0-9]).{5,}$' THEN
        SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Lỗi: PASSWORD phải có ít nhất 1 chữ, 1 số và dài tối thiểu 5 ký tự!';
    END IF;
    -- USERNAME không được trùng
    SELECT COUNT(*) INTO v_COUNT
    FROM USERS
    WHERE USERNAME = NEW.USERNAME;
    IF v_COUNT > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Lỗi: USERNAME đã tồn tại, vui lòng chọn tên khác!';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Dumping events for database 'E-commerce-php'
--

--
-- Dumping routines for database 'E-commerce-php'
--
/*!50003 DROP PROCEDURE IF EXISTS `DELETE_PRODUCTS` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `DELETE_PRODUCTS`(IN p_PRODUCTID VARCHAR(50))
BEGIN
    -- Kiểm tra sản phẩm có tồn tại không
    IF NOT EXISTS (SELECT 1 FROM PRODUCTS WHERE PRODUCTID = p_PRODUCTID) THEN
        SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Lỗi: Không tìm thấy sản phẩm trong bảng products!';
    END IF;
    -- Kiểm tra sản phẩm đang có số lượng trong bảng updates
    IF EXISTS (
        SELECT 1 
        FROM UPDATES 
        WHERE PRODUCTID = p_PRODUCTID AND PRO_QUANTITY > 0
    ) THEN
        SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Lỗi: Sản phẩm vẫn còn số lượng trong bảng updates, không thể xoá!';
    END IF;
    -- Xóa sản phẩm
    DELETE FROM PRODUCTS WHERE PRODUCTID = p_PRODUCTID;
    SELECT CONCAT('Đã xoá thành công sản phẩm: ', p_PRODUCTID) AS MESSAGE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `INSERT_PAYMENTS` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `INSERT_PAYMENTS`(
	IN p_ORDERID VARCHAR(10),
     	IN p_PAY_NUMBER VARCHAR(10),
    	IN p_PAY_METHOD VARCHAR(20),
     	IN p_STATUS_OF_ORDER VARCHAR(20))
BEGIN
    -- Kiểm tra phương thức thanh toán
    IF p_PAY_METHOD NOT IN ("Tiền mặt", "Chuyển khoản") THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = "Lỗi: Phương thức thanh toán không hợp lệ. Chỉ được Tiền mặt hoặc Chuyển khoản";
    END IF;
    -- Kiểm tra trùng lặp của mã thanh toán
    IF EXISTS(SELECT p_PAY_NUMBER = 1 FROM PAYMENTS WHERE PAY_NUMBER = p_PAY_NUMBER) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = "Lỗi: Mã thanh toán đã tồn tại trong hệ thống";
    END IF;
    -- Thêm dữ liệu
    INSERT INTO PAYMENTS (ORDERID, PAY_NUMBER, PAY_TIME, PAY_DATE, PAY_METHOD, STATUS_OF_ORDER)
    VALUES (p_ORDERID, p_PAY_NUMBER, CURTIME(), CURDATE(), p_PAY_METHOD, p_STATUS_OF_ORDER);
    SELECT CONCAT("Thêm thanh toán ", p_PAY_NUMBER, " thành công cho đơn hàng ", p_ORDERID) AS MESSAGE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `UPDATE_PRODUCTS` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `UPDATE_PRODUCTS`(
	IN p_PRODUCTID VARCHAR(50),
    	IN p_SELLERID VARCHAR(10),
    	IN p_PRO_QUANTITY INT)
BEGIN
    DECLARE v_CURRENT_QUANTITY INT;
    DECLARE v_PRODUCTID_EXIST INT;
    DECLARE v_PRO_SELLERID INT;
    -- Lấy số lượng hiện tại
    SELECT PRO_QUANTITY INTO v_CURRENT_QUANTITY
    FROM UPDATES
    WHERE PRODUCTID = p_PRODUCTID AND SELLERID = p_SELLERID;
    -- Kiểm tra sản phẩm tồn tại
    SELECT COUNT(*) INTO v_PRODUCTID_EXIST
    FROM UPDATES
    WHERE PRODUCTID = p_PRODUCTID;
    
    IF  v_PRODUCTID_EXIST = 0 THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = "Lỗi: Sản phẩm không tồn tại trong hệ thống";
    END IF;
	-- Kiểm tra sản phẩm của người bán
    SELECT COUNT(*) INTO v_PRO_SELLERID
    FROM UPDATES
    WHERE PRODUCTID = p_PRODUCTID AND SELLERID = p_SELLERID;
    
    IF  v_PRO_SELLERID = 0 THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = "Lỗi: Người bán không bán sản phẩm này";
    END IF;
    -- Cập nhật dữ liệu
    UPDATE UPDATES
    SET  PRO_QUANTITY = p_PRO_QUANTITY, 
		 PRO_STATUS = CASE WHEN p_PRO_QUANTITY > 0 THEN 'Còn hàng' ELSE 'Hết hàng' END
    WHERE PRODUCTID = p_PRODUCTID AND SELLERID = p_SELLERID;
    SELECT CONCAT("Đã cập nhật số lượng sản phẩm ", p_PRODUCTID, " thành ", p_PRO_QUANTITY) AS MESSAGE;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-15 20:35:42
