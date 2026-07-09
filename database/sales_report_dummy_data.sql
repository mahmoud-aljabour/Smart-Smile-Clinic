-- ============================================================
-- Dental Clinic - بيانات وهمية لتقرير المبيعات (Sales Report)
-- Database: db-dental-07-10
-- ============================================================
-- الاستخدام: نفّذ هذا الملف من phpMyAdmin أو MySQL CLI
--   mysql -u root "db-dental-07-10" < database/sales_report_dummy_data.sql
-- ============================================================

USE `db-dental-07-10`;

-- ------------------------------------------------------------
-- 1) خدمات إضافية (اختياري - لتنويع التقرير)
-- ------------------------------------------------------------
INSERT INTO `tblservices` (`SKU`, `ToothNumber`, `AgeGroupID`, `Services`, `Description`, `OriginalPrice`) VALUES
('2025-10-7', 0, 0, 'Teeth Cleaning', 'Professional cleaning', 80),
('2025-10-8', 0, 0, 'Root Canal', '', 250),
('2025-10-9', 11, 0, 'Dental Filling', '', 120),
('2025-10-10', 0, 0, 'X-Ray', 'Panoramic', 45),
('2025-10-11', 0, 0, 'Tooth Extraction', '', 90);

-- ------------------------------------------------------------
-- 2) فواتير مدفوعة (tblpayments) + بنود الفاتورة (tblinvoice)
--    التقرير يعرض فقط: Status = 'Paid'
-- ------------------------------------------------------------

-- فاتورة 1 - 15/01/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-01-15', '2026-01-15', 'INV_202520', 3, 177, 177, 0, '2026-01-15 10:30:00', 'ahmed abed abdo', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202520', '2025-10-5', 32, '32th', 52, 1, 52, '', 'USER_01', 'Invoice'),
('INV_202520', '2025-10-10', 0, 'X-Ray ( Panoramic )', 45, 1, 45, '', 'USER_01', 'Invoice'),
('INV_202520', '2025-10-7', 0, 'Teeth Cleaning ( Professional cleaning )', 80, 1, 80, '', 'USER_01', 'Invoice');

-- فاتورة 2 - 22/01/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-01-22', '2026-01-22', 'INV_202521', 1, 57, 57, 0, '2026-01-22 14:15:00', 'ahmed ali jabour', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202521', '2025-10-6', 0, 'Tooth', 57, 1, 57, '', 'USER_01', 'Invoice');

-- فاتورة 3 - 05/02/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-02-05', '2026-02-05', 'INV_202522', 3, 245, 245, 0, '2026-02-05 09:00:00', 'mahmoud maher aljabour', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202522', '2025-10-9', 11, 'Dental Filling', 120, 1, 120, '', 'USER_01', 'Invoice'),
('INV_202522', '2025-10-10', 0, 'X-Ray ( Panoramic )', 45, 1, 45, '', 'USER_01', 'Invoice'),
('INV_202522', '2025-10-7', 0, 'Teeth Cleaning ( Professional cleaning )', 80, 1, 80, '', 'USER_01', 'Invoice');

-- فاتورة 4 - 18/02/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-02-18', '2026-02-18', 'INV_202523', 1, 250, 250, 0, '2026-02-18 11:45:00', 'ahmed abed abdo', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202523', '2025-10-8', 0, 'Root Canal', 250, 1, 250, '', 'USER_01', 'Invoice');

-- فاتورة 5 - 10/03/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-03-10', '2026-03-10', 'INV_202524', 2, 142, 142, 0, '2026-03-10 16:20:00', 'ahmed dsd sdsd', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202524', '2025-10-11', 0, 'Tooth Extraction', 90, 1, 90, '', 'USER_01', 'Invoice'),
('INV_202524', '2025-10-5', 32, '32th', 52, 1, 52, '', 'USER_01', 'Invoice');

-- فاتورة 6 - 25/03/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-03-25', '2026-03-25', 'INV_202525', 1, 80, 80, 0, '2026-03-25 08:30:00', 'ahmed ali jabour', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202525', '2025-10-7', 0, 'Teeth Cleaning ( Professional cleaning )', 80, 1, 80, '', 'USER_01', 'Invoice');

-- فاتورة 7 - 12/04/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-04-12', '2026-04-12', 'INV_202526', 3, 352, 352, 0, '2026-04-12 13:00:00', 'mahmoud maher aljabour', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202526', '2025-10-8', 0, 'Root Canal', 250, 1, 250, '', 'USER_01', 'Invoice'),
('INV_202526', '2025-10-10', 0, 'X-Ray ( Panoramic )', 45, 1, 45, '', 'USER_01', 'Invoice'),
('INV_202526', '2025-10-6', 0, 'Tooth', 57, 1, 57, '', 'USER_01', 'Invoice');

-- فاتورة 8 - 08/05/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-05-08', '2026-05-08', 'INV_202527', 1, 120, 120, 0, '2026-05-08 10:10:00', 'ahmed abed abdo', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202527', '2025-10-9', 11, 'Dental Filling', 120, 1, 120, '', 'USER_01', 'Invoice');

-- فاتورة 9 - 20/05/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-05-20', '2026-05-20', 'INV_202528', 2, 142, 142, 0, '2026-05-20 15:40:00', 'ahmed dsd sdsd', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202528', '2025-10-5', 32, '32th', 52, 1, 52, '', 'USER_01', 'Invoice'),
('INV_202528', '2025-10-11', 0, 'Tooth Extraction', 90, 1, 90, '', 'USER_01', 'Invoice');

-- فاتورة 10 - 03/06/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-06-03', '2026-06-03', 'INV_202529', 1, 57, 57, 0, '2026-06-03 09:25:00', 'ahmed ali jabour', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202529', '2025-10-6', 0, 'Tooth', 57, 1, 57, '', 'USER_01', 'Invoice');

-- فاتورة 11 - 17/06/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-06-17', '2026-06-17', 'INV_202530', 2, 200, 200, 0, '2026-06-17 12:00:00', 'mahmoud maher aljabour', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202530', '2025-10-7', 0, 'Teeth Cleaning ( Professional cleaning )', 80, 1, 80, '', 'USER_01', 'Invoice'),
('INV_202530', '2025-10-9', 11, 'Dental Filling', 120, 1, 120, '', 'USER_01', 'Invoice');

-- فاتورة 12 - 01/07/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-07-01', '2026-07-01', 'INV_202531', 1, 90, 90, 0, '2026-07-01 11:00:00', 'ahmed abed abdo', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202531', '2025-10-11', 0, 'Tooth Extraction', 90, 1, 90, '', 'USER_01', 'Invoice');

-- فاتورة 13 - 05/07/2026
INSERT INTO `tblpayments` (`ORNO`, `InvoiceDate`, `DateDue`, `InvoiceNo`, `TotalQTY`, `TotalAmount`, `Payment`, `Balance`, `PaymentDate`, `Patients`, `UserID`, `Status`, `Class`) VALUES
('', '2026-07-05', '2026-07-05', 'INV_202532', 2, 97, 97, 0, '2026-07-05 14:30:00', 'ahmed ali jabour', 'USER_01', 'Paid', 'Invoice');

INSERT INTO `tblinvoice` (`InvoiceNo`, `SKU`, `ToothNumber`, `Services`, `Price`, `QTY`, `SubTotal`, `Remarks`, `UserID`, `Class`) VALUES
('INV_202532', '2025-10-10', 0, 'X-Ray ( Panoramic )', 45, 1, 45, '', 'USER_01', 'Invoice'),
('INV_202532', '2025-10-5', 32, '32th', 52, 1, 52, '', 'USER_01', 'Invoice');

-- ------------------------------------------------------------
-- 3) تحديث رقم الفاتورة التلقائي
-- ------------------------------------------------------------
UPDATE `tblautonumbers` SET `AUTOEND` = 33 WHERE `AUTOKEY` = 'INVOICENO';

-- ------------------------------------------------------------
-- للتحقق بعد التنفيذ:
-- SELECT COUNT(*) FROM tblpayments WHERE Status='Paid';
-- SELECT i.Services, i.Price, p.InvoiceDate, p.InvoiceNo
-- FROM tblinvoice i INNER JOIN tblpayments p ON i.InvoiceNo = p.InvoiceNo
-- WHERE p.Status = 'Paid' AND p.InvoiceDate BETWEEN '2026-01-01' AND '2026-07-31';
-- ============================================================
