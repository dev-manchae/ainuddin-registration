# Future Recommendations & System Roadmap

This document outlines the deferred feature specifications and system recommendations for the Tahfiz Ainuddin Registration System. These features are designed to scale the system as the school commences operations and expands its administrative requirements.

---

## 1. Simulated Payment Step & Receipt Upload (Option B)

### Objective
Provide parents with instructions to pay registration fees and upload a transaction receipt, enabling administrators to verify the payment status before final enrollment.

### Workflow & User Experience
1. **Parent Upload**: After submitting the registration form (or once the application status is marked as "Diluluskan" / "Lulus Temuduga"), the parent is guided to a **Payment Dashboard**.
   * Displays payment amount (e.g. RM 50.00), bank account details, and payment reference.
   * Provides a drag-and-drop file upload zone for transaction receipts (PNG, JPG, PDF).
2. **Admin Verification**: Administrators access a dedicated **Pengesahan Pembayaran (Payment Verification)** queue.
   * Admins view receipt previews alongside student/parent metadata.
   * Admins click "Sahkan Pembayaran" (Approve) or "Tolak & Minta Muat Naik Semula" (Reject with notes).
3. **Email Notification**: Automatic system emails are dispatched to notify parents of payment validation or requests for receipt re-upload.

### Proposed Database Schema
A new table `pembayaran` will track transactions linked to applications:

```sql
CREATE TABLE pembayaran (
    id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
    id_permohonan INT NOT NULL,
    jumlah DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    bukti_fail VARCHAR(255) NOT NULL,
    status_pembayaran ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    catatan TEXT,
    tarikh_muat_naik TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tarikh_pengesahan TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    disahkan_oleh INT,
    FOREIGN KEY (id_permohonan) REFERENCES permohonan(id_permohonan) ON DELETE CASCADE,
    FOREIGN KEY (disahkan_oleh) REFERENCES pengguna(id_pengguna)
);
```

---

## 2. Waitlist & Capacity Queue Management (Option C)

### Objective
Automatically handle intake quota overflows by transitioning applicants to a waiting list queue when active intake limits are exceeded.

### Workflow & User Experience
1. **Quota Calculation**: The system monitors the count of applications marked `04` (Diluluskan) or `03` (Dihantar) against the active intake's `kuota` column.
2. **Auto-Waitlist State**: Once active submissions equal or exceed the quota:
   * The parent dashboard displays a banner: "Sesi Pendaftaran Penuh - Permohonan Seterusnya Akan Dimasukkan ke Barisan Menunggu (Waitlist)".
   * New submissions receive a status code of `09` (Barisan Menunggu).
3. **Admin Promotion Queue**: Administrators can view a prioritized queue of waitlisted applicants. If an approved student withdraws, the admin can manually "promote" the next waitlisted applicant to active review status.

### Proposed Database Schema
Add a `waiting_list_priority` column to `permohonan` or implement a dedicated queue table:

```sql
CREATE TABLE barisan_menunggu (
    id_queue INT AUTO_INCREMENT PRIMARY KEY,
    id_permohonan INT NOT NULL,
    id_intake INT NOT NULL,
    posisi_barisan INT NOT NULL,
    tarikh_ditambah TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_permohonan) REFERENCES permohonan(id_permohonan) ON DELETE CASCADE,
    FOREIGN KEY (id_intake) REFERENCES intake_batch(id_intake) ON DELETE CASCADE
);
```

---

## 3. SMS Notification Gateway

### Objective
Support transactional SMS alerts in addition to simulated emails for critical application updates (e.g. interview invitations, corrections needed, approvals).

### Workflow & Integration
1. **Third-Party API**: Integrate with local SMS service providers (such as BulkSMS Malaysia or SMS.to) or Twilio.
2. **Trigger Hook**: Hook SMS dispatches inside the controllers when status changes occur.
3. **Simulated Dashboard Logs**: For developer testing, add an admin page to view dispatched SMS logs before going live with paid credits.

---

## 4. Admin Dashboard Demographic Analytics

### Objective
Present administrators with real-time statistics and graphical distributions of the applicant pool for strategic planning.

### Proposed Visual Analytics
* **Applicant Distribution**: Bar/donut charts showing applicants by Cawangan MTA, Gender, and Age.
* **Academic & Quranic Level Charting**: Stacked bar charts showing the count of students categorized by their Quranic mastery levels (e.g., Lancar Membaca, Merangkak, Iqra).
* **Weekly Registration Volatility**: A line chart mapping registration volume over the active intake period.
* **Implementation Stack**: Leverage **Chart.js** via CDN inside a sleek, interactive administrative stats panel.
