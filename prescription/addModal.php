<?php
class Prescription {
    // Properties
    public $PrescriptionID;
    public $PatientID;
    public $InvoiceNo;
    public $Date;
    public $DoctorID;
    public $Medications;
    public $Dosage;
    public $Instructions;
    public $Description;
    public $Signature;
    public $CreatedBy;
    public $CreatedDate;

    // Constructor
    public function __construct() {
        // Default constructor
    }

    // Create new prescription
    public function create() {
        global $mydb;
        $sql = "INSERT INTO tblprescriptions (PatientID, InvoiceNo, Date, DoctorID, Medications, Dosage, Instructions, Description, Signature, CreatedBy) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $mydb->setQuery($sql);
        $mydb->executeQuery($this->PatientID, $this->InvoiceNo, $this->Date, $this->DoctorID, $this->Medications, $this->Dosage, $this->Instructions, $this->Description, $this->Signature, $this->CreatedBy);
        return $mydb->getLastInsertID(); // Return the new ID
    }

    // Update existing prescription
    public function update($id) {
        global $mydb;
        $sql = "UPDATE tblprescriptions SET PatientID = ?, InvoiceNo = ?, Date = ?, DoctorID = ?, 
                Medications = ?, Dosage = ?, Instructions = ?, Description = ?, Signature = ? 
                WHERE PrescriptionID = ?";
        $mydb->setQuery($sql);
        $mydb->executeQuery($this->PatientID, $this->InvoiceNo, $this->Date, $this->DoctorID, 
                            $this->Medications, $this->Dosage, $this->Instructions, $this->Description, $this->Signature, $id);
    }

    // Delete prescription
    public function delete($id) {
        global $mydb;
        $sql = "DELETE FROM tblprescriptions WHERE PrescriptionID = ?";
        $mydb->setQuery($sql);
        $mydb->executeQuery($id);
    }

    // Load single prescription by ID
    public function loadByID($id) {
        global $mydb;
        $sql = "SELECT * FROM tblprescriptions WHERE PrescriptionID = ?";
        $mydb->setQuery($sql);
        return $mydb->loadSingleResult($id);
    }

    // Load prescriptions by PatientID
    public function loadByPatient($patientID) {
        global $mydb;
        $sql = "SELECT * FROM tblprescriptions WHERE PatientID = ? ORDER BY CreatedDate DESC";
        $mydb->setQuery($sql);
        return $mydb->loadResultList($patientID);
    }

    // Load prescriptions by InvoiceNo
    public function loadByInvoice($invno) {
        global $mydb;
        $sql = "SELECT * FROM tblprescriptions WHERE InvoiceNo = ? ORDER BY CreatedDate DESC";
        $mydb->setQuery($sql);
        return $mydb->loadResultList($invno);
    }
}
?>