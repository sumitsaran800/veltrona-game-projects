V28 WorkOrder Field Fix

Fix applied in api/_router.php:
- GetFormFieldList now supports serviceId, formId, id, typeId, typeid, typeld.
- Response now includes data.list plus data.formFields/fieldList/fields.
- IFSC, Deposit Not Received, Withdraw Problem, Change Password, Change Bank, USDT, Game Problem forms now return proper fields instead of blank page.

Test URLs:
/api/WorkOrder/GetFormFieldList.php?serviceId=90&typeId=11
/api/WorkOrder/GetFormFieldList.php?serviceId=91&typeId=4
/api/GetFormFieldList.php?serviceId=90&typeId=11  (if your route uses this path)

Expected: data.totalCount > 0 and data.list is not empty.

Install:
Upload all files except img folder. Clear browser cache + unregister service worker once after upload.
