-- MySQL dump 10.13  Distrib 8.0.15, for linux-glibc2.12 (x86_64)
--
-- Host: localhost    Database: bitnami_wordpress
-- ------------------------------------------------------
-- Server version	8.0.15

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES UTF8 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wp_applicant`
--

DROP TABLE IF EXISTS `wp_applicant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `wp_applicant` (
  `applicant` varchar(32) NOT NULL,
  `mail` varchar(400) NOT NULL,
  `agree1` tinyint(1) NOT NULL,
  `agree2` tinyint(1) NOT NULL,
  `apply_service` char(1) DEFAULT NULL,
  `apply_plan` char(1) DEFAULT NULL,
  `biz_fg` char(1) DEFAULT NULL COMMENT '事業形態（0:法人、1:個人事業主）',
  `biz_number` char(13) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL COMMENT '社名',
  `company_name_kana` varchar(160) DEFAULT NULL COMMENT '社名（フリカナ）',
  `zip` varchar(10) DEFAULT NULL COMMENT '郵便番号',
  `pref` varchar(10) DEFAULT NULL COMMENT '都道府県',
  `addr` varchar(100) DEFAULT NULL COMMENT '市区群',
  `addr2` varchar(100) DEFAULT NULL COMMENT '町名番地',
  `addr3` varchar(100) DEFAULT NULL COMMENT 'ビル名',
  `addr_kana` varchar(160) DEFAULT NULL COMMENT '住所（フリカナ）',
  `tel` varchar(24) DEFAULT NULL COMMENT '電話番号',
  `fax` varchar(24) DEFAULT NULL COMMENT 'FAX',
  `est_dt` varchar(24) DEFAULT NULL,
  `num_employ` varchar(24) DEFAULT NULL,
  `capital` varchar(24) DEFAULT NULL,
  `annual_sales` varchar(24) DEFAULT NULL,
  `goods_class` char(1) DEFAULT NULL,
  `goods` varchar(100) DEFAULT NULL COMMENT '取扱商材',
  `delivery_company` varchar(100) DEFAULT NULL COMMENT '代引契約している配送会社',
  `url` varchar(100) DEFAULT NULL COMMENT 'ホームページURL',
  `ceo_name_sei` varchar(40) DEFAULT NULL COMMENT '代表者名（姓）',
  `ceo_name_mei` varchar(40) DEFAULT NULL COMMENT '代表者名（名）',
  `ceo_name_kana_sei` varchar(80) DEFAULT NULL COMMENT '代表者名（セイ）',
  `ceo_name_kana_mei` varchar(80) DEFAULT NULL COMMENT '代表者名（メイ）',
  `ceo_birth` varchar(24) DEFAULT NULL,
  `ceo_addr_fg` varchar(160) DEFAULT NULL COMMENT '代表者自宅住所',
  `ceo_zip` varchar(10) DEFAULT NULL COMMENT '代表者自宅郵便番号',
  `ceo_pref` varchar(10) DEFAULT NULL COMMENT '代表者自宅都道府県',
  `ceo_addr1` varchar(100) DEFAULT NULL COMMENT '代表者自宅市区群',
  `ceo_addr2` varchar(100) DEFAULT NULL COMMENT '代表者自宅町名番地',
  `ceo_addr3` varchar(100) DEFAULT NULL COMMENT '代表者自宅ビル名',
  `ceo_addr_kana` varchar(160) DEFAULT NULL COMMENT '代表者自宅住所（フリガナ）',
  `ceo_tel` varchar(24) DEFAULT NULL COMMENT '代表者自宅電話番号',
  `corp_fg` char(1) DEFAULT NULL COMMENT '社名（0:お申込者と同じ、1:個別に設定する）',
  `staff_company_name` varchar(100) DEFAULT NULL COMMENT '社名',
  `staff_company_name_kana` varchar(160) DEFAULT NULL COMMENT '社名（フリカナ）',
  `staff_name_sei` varchar(40) DEFAULT NULL COMMENT 'ご担当者名（姓）',
  `staff_name_mei` varchar(40) DEFAULT NULL COMMENT 'ご担当者名（名）',
  `staff_name_kana_sei` varchar(80) DEFAULT NULL COMMENT 'ご担当者名（セイ）',
  `staff_name_kana_mei` varchar(80) DEFAULT NULL COMMENT 'ご担当者名（メイ）',
  `staff_mail` varchar(400) DEFAULT NULL COMMENT 'メールアドレス',
  `staff_section` varchar(100) DEFAULT NULL COMMENT '部署',
  `staff_post` varchar(100) DEFAULT NULL COMMENT '役職',
  `staff_tel` varchar(24) DEFAULT NULL COMMENT '連絡用電話番号',
  `staff_fax` varchar(24) DEFAULT NULL COMMENT '連絡用FAX番号',
  `staff_addr_fg` char(1) DEFAULT NULL COMMENT '住所（0:お申込者と同じ、1:個別に設定する）',
  `staff_zip` varchar(10) DEFAULT NULL COMMENT '郵便番号',
  `staff_pref` varchar(10) DEFAULT NULL COMMENT '都道府県',
  `staff_addr1` varchar(100) DEFAULT NULL COMMENT '市区群',
  `staff_addr2` varchar(100) DEFAULT NULL COMMENT '町名番地',
  `staff_addr3` varchar(100) DEFAULT NULL COMMENT 'ビル名',
  `staff_addr_kana` varchar(160) DEFAULT NULL COMMENT '住所（フリカナ）',
  `invoice_fg` char(1) DEFAULT NULL COMMENT '（0:運用ご担当者と同じ、1:個別に設定する）',
  `invoice_company_name_fg` char(1) DEFAULT NULL,
  `invoice_company_name` varchar(100) DEFAULT NULL COMMENT '社名',
  `invoice_company_name_kana` varchar(160) DEFAULT NULL COMMENT '社名（フリカナ）',
  `invoice_name_sei` varchar(40) DEFAULT NULL COMMENT 'ご担当者名（姓）',
  `invoice_name_mei` varchar(40) DEFAULT NULL COMMENT 'ご担当者名（名）',
  `invoice_name_kana_sei` varchar(80) DEFAULT NULL COMMENT 'ご担当者名（セイ）',
  `invoice_name_kana_mei` varchar(80) DEFAULT NULL COMMENT 'ご担当者名（メイ）',
  `invoice_section` varchar(100) DEFAULT NULL COMMENT '部署',
  `invoice_post` varchar(100) DEFAULT NULL COMMENT '役職',
  `invoice_tel` varchar(24) DEFAULT NULL COMMENT '連絡用電話番号',
  `invoice_fax` varchar(24) DEFAULT NULL COMMENT '連絡用FAX番号',
  `invoice_addr_fg` char(1) DEFAULT NULL COMMENT '（0:お申込者と同じ、1:個別に設定する）',
  `invoice_zip` varchar(10) DEFAULT NULL COMMENT '郵便番号',
  `invoice_pref` varchar(10) DEFAULT NULL COMMENT '都道府県',
  `invoice_addr1` varchar(100) DEFAULT NULL COMMENT '市区群',
  `invoice_addr2` varchar(100) DEFAULT NULL COMMENT '町名番地',
  `invoice_addr3` varchar(100) DEFAULT NULL COMMENT 'ビル名',
  `invoice_addr_kana` varchar(160) DEFAULT NULL COMMENT '住所（フリカナ）',
  `fin_name` varchar(100) DEFAULT NULL COMMENT '金融機関名',
  `fin_branch_name` varchar(100) DEFAULT NULL COMMENT '支店名',
  `fin_account_type` varchar(100) DEFAULT NULL COMMENT '口座種別',
  `fin_account_number` varchar(24) DEFAULT NULL,
  `fin_account_name` varchar(100) DEFAULT NULL COMMENT '口座名義',
  `fin_account_name_kana` varchar(160) DEFAULT NULL COMMENT '口座名義（フリカナ）',
  `goods_name1` varchar(100) DEFAULT NULL COMMENT '商品名①',
  `goods_price1` varchar(24) DEFAULT NULL,
  `goods_image1` varchar(100) DEFAULT NULL COMMENT '商品画像①（ファイルパス）',
  `goods_name2` varchar(100) DEFAULT NULL COMMENT '商品名②',
  `goods_price2` varchar(24) DEFAULT NULL,
  `goods_image2` varchar(100) DEFAULT NULL COMMENT '商品画像②（ファイルパス）',
  `goods_name3` varchar(100) DEFAULT NULL COMMENT '商品名③',
  `goods_price3` varchar(24) DEFAULT NULL,
  `goods_image3` varchar(100) DEFAULT NULL COMMENT '商品画像③（ファイルパス）',
  `price_range_min` varchar(50) DEFAULT NULL COMMENT '商品価格帯（目安）',
  `price_range_max` varchar(50) DEFAULT NULL,
  `other_site_url` varchar(100) DEFAULT NULL COMMENT '他サイトURL',
  `distributor` varchar(100) DEFAULT NULL COMMENT '販売事業者',
  `corp_name` varchar(100) DEFAULT NULL COMMENT 'ショップ名（15文字以内）',
  `corp_name_kana` varchar(160) DEFAULT NULL COMMENT 'ショップ名（フリカナ）',
  `corp_name_en` varchar(100) DEFAULT NULL COMMENT 'ショップ名（英字表記）',
  `location_fg` char(1) DEFAULT NULL COMMENT '所在地（0:お申込者と同じ、1:個別に設定する）',
  `supervisor_zip` varchar(10) DEFAULT NULL COMMENT '郵便番号',
  `supervisor_pref` varchar(10) DEFAULT NULL COMMENT '都道府県',
  `supervisor_addr` varchar(100) DEFAULT NULL COMMENT '市区群',
  `supervisor_addr2` varchar(100) DEFAULT NULL COMMENT '町名番地',
  `supervisor_addr3` varchar(100) DEFAULT NULL COMMENT 'ビル名',
  `supervisor_addr_kana` varchar(160) DEFAULT NULL COMMENT '住所（フリカナ）',
  `supervisor_fg` char(1) DEFAULT NULL COMMENT '運営統括責任者（0:代表者名と同じ、1:個別に設定する）',
  `supervisor_name_sei` varchar(40) DEFAULT NULL COMMENT '運営統括責任者名（姓）',
  `supervisor_name_mei` varchar(40) DEFAULT NULL COMMENT '運営統括責任者名（名）',
  `supervisor_mail` varchar(400) DEFAULT NULL COMMENT 'メールアドレス',
  `supervisor_tel` varchar(24) DEFAULT NULL COMMENT '電話番号',
  `supervisor_fax` varchar(24) DEFAULT NULL COMMENT 'FAX番号',
  `contact_s_time` time DEFAULT NULL COMMENT '問合せ受付可能営業時間（開始）',
  `contact_e_time` time DEFAULT NULL COMMENT '問合せ受付可能営業時間（終了）',
  `expenses` varchar(16) DEFAULT NULL,
  `expenses_other` varchar(100) DEFAULT NULL COMMENT '商品以外の必要代金：その他',
  `defective` char(1) DEFAULT NULL,
  `defective_other` varchar(100) DEFAULT NULL COMMENT '不良品の取扱：その他',
  `sales_qty` int(11) DEFAULT NULL COMMENT '販売数量',
  `sales_qty_other` varchar(100) DEFAULT NULL COMMENT '販売数量：その他',
  `delivery_time` varchar(100) DEFAULT NULL,
  `delivery_time_none` varchar(100) DEFAULT NULL,
  `payment` varchar(16) DEFAULT NULL,
  `payment_other` varchar(100) DEFAULT NULL COMMENT '支払方法：その他',
  `due_payment` varchar(100) DEFAULT NULL COMMENT '支払期限',
  `about_returns` char(1) DEFAULT NULL COMMENT '返品について',
  `about_returns_other` varchar(100) DEFAULT NULL,
  `due_returns` varchar(100) DEFAULT NULL COMMENT '返品期限',
  `return_shipping` varchar(16) DEFAULT NULL,
  `return_shipping_other` varchar(100) DEFAULT NULL COMMENT '返品送料：その他',
  `vt_ch1` char(1) DEFAULT '2',
  `vt_ch2` char(1) DEFAULT '2',
  `vt_ch3` char(1) DEFAULT '2',
  `vt_ch4` char(1) DEFAULT '2',
  `vt_ch5` char(1) DEFAULT '2',
  `vt_ch6` char(1) DEFAULT '2',
  `vt_ch7` char(1) DEFAULT '1',
  `vt_ch8` char(1) DEFAULT '2',
  `vt_ch9` char(1) DEFAULT '2',
  `vt_ch10` char(1) DEFAULT '2',
  `vt_ch11` char(1) DEFAULT '2',
  `status` char(5) NOT NULL DEFAULT '00000',
  `shop_category` varchar(8) DEFAULT NULL COMMENT 'ショップ分類',
  `open_dt` datetime DEFAULT NULL COMMENT '開店日',
  `close_dt` datetime DEFAULT NULL COMMENT '閉店日',
  `remark` varchar(400) DEFAULT NULL COMMENT '管理者メモ',
  `field1` text COMMENT '自由記入フィールド１',
  `field2` text COMMENT '自由記入フィールド２',
  `field3` text COMMENT '自由記入フィールド３',
  `message` text COMMENT 'モールからのメッセージ',
  `rgdt` datetime DEFAULT NULL COMMENT '登録日',
  `updt` datetime DEFAULT NULL COMMENT '最終更新日',
  `upuser` varchar(32) DEFAULT NULL COMMENT '最終更新者',
  PRIMARY KEY (`applicant`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-08-15  8:14:25
