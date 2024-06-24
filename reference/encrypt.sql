UPDATE  fss_tbl_invalid_log SET STATUS = 2;

UPDATE  fss_tbl_user SET is_active = 1 WHERE is_active = 2;
UPDATE fss_tbl_user 
SET encrypt_value = aes_encrypt(CONCAT_WS(', ', user_id, user_name, user_email, password, user_type_id, is_active, 
require_changepassword, IFNULL(modified_date, '') ), 'globalfileshelving2018');

UPDATE fss_tbl_customer SET is_lock = 0;
UPDATE fss_tbl_customer 
SET encrypt_value = aes_encrypt(CONCAT_WS(', ', customer_id, customer_name, IFNULL(nrc_division_code, '') , 
IFNULL(nrc_township_code, '') , IFNULL(nrc_citizen_type, '') , IFNULL(nrc_number, '') , IFNULL(nrc_text, '') , IFNULL(passport, '') , 
father_name, IFNULL(date_of_birth, '') , street, house_no, IFNULL(division_id, '') , IFNULL(township_id, '') , IFNULL(ward_id, '') , 
created_by, created_date, IFNULL(modified_by, '') , IFNULL(modified_date, '') , is_lock), 'globalfileshelving2018');

UPDATE fss_tbl_folder SET is_lock = 0;
UPDATE fss_tbl_folder 
SET encrypt_value = aes_encrypt(CONCAT_WS(', ', folder_id, file_type_id, IFNULL(rfid_no, '') , IFNULL(description, '') , folder_no, 
security_type_id, IFNULL(shelf_id, '') , shelf_row, shelf_column, IFNULL(destroy_date, '') , IFNULL(destroy_order_no, '') , 
IFNULL(destroy_remark, '') , IFNULL(destroy_order_employeeid, '') , IFNULL(destroy_order_employee_name, '') , 
IFNULL(destroy_order_designation, '') , IFNULL(destroy_order_department, '') , IFNULL(destroy_duty_employeeid, '') , 
IFNULL(destroy_duty_employee_name, '') , IFNULL(destroy_duty_designation, '') , IFNULL(destroy_duty_department, '') , status, 
created_by, created_date, IFNULL(modified_by, '') , IFNULL(modified_date, '') , is_lock), 'globalfileshelving2018');

UPDATE fss_tbl_folder_transaction 
SET encrypt_value = aes_encrypt(CONCAT_WS(', ', transaction_id, folder_id, taken_date, IFNULL(given_date, '') , taken_employeeid, 
taken_employee_name, taken_designation, taken_department, IFNULL(given_employeeid, '') , IFNULL(given_employee_name, '') , 
IFNULL(given_designation, '') , IFNULL(given_department, '') , IFNULL(remark, '') , created_by, created_date, 
IFNULL(modified_by, '') , IFNULL(modified_date, '') ), 'globalfileshelving2018');

UPDATE fss_tbl_file 
SET encrypt_value = aes_encrypt(CONCAT_WS(', ', file_id, folder_id, IFNULL(letter_no, '') , letter_count, letter_date, 
IFNULL(description, '') , IFNULL(to_do, '') , IFNULL(remark, '') , from_department_type, IFNULL(from_department_id, '') , 
to_department_type, IFNULL(security_type_id, '') , IFNULL(application_type_id, '') , IFNULL(application_description, '') , 
IFNULL(application_references, '') , IFNULL(receiver_customer_id, '') , IFNULL(sender_customer_id, '') , IFNULL(destroy_date, '') , 
IFNULL(destroy_order_no, '') , IFNULL(destroy_remark, '') , IFNULL(destroy_order_employeeid, '') , 
IFNULL(destroy_order_employee_name, '') , IFNULL(destroy_order_designation, '') , IFNULL(destroy_order_department, '') , 
IFNULL(destroy_duty_employeeid, '') , IFNULL(destroy_duty_employee_name, '') , IFNULL(destroy_duty_designation, '') , 
IFNULL(destroy_duty_department, '') , status, created_by, created_date, IFNULL(modified_by, '') , 
IFNULL(modified_date, '') ), 'globalfileshelving2018');

UPDATE fss_tbl_file_transaction 
SET encrypt_value = aes_encrypt(CONCAT_WS(', ', file_transaction_id, IFNULL(folder_transaction_id, '') , file_id, taken_date, 
IFNULL(given_date, '') , taken_employeeid, taken_employee_name, taken_designation, taken_department, 
IFNULL(given_employeeid, '') , IFNULL(given_employee_name, '') , IFNULL(given_designation, '') , IFNULL(given_department, '') , 
IFNULL(remark, '') , created_by, created_date, IFNULL(modified_by, '') , IFNULL(modified_date, '') ), 'globalfileshelving2018');
