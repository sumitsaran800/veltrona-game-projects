<?php
header('Content-Type: application/json; charset=utf-8');
header('Strict-Transport-Security: max-age=31536000');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Credentials: true');
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
header('Access-Control-Allow-Origin: ' . $origin);
header('vary: Origin');

date_default_timezone_set("Asia/Kolkata");
$shonubody = file_get_contents("php://input");
$payload = json_decode($shonubody, true);


$res = [
    "data" => [],
    "msgParameters" => null,
    "code" => 0,
    "msg" => "Succeed",
    "msgCode" => 0
];

if (!isset($payload['formId'])) {
    $res['data'] = [
        'error' => 'formId not provided'
    ];
    $res['msg'] = 'formId not provided';
    $res['code'] = 400; 
} elseif ($payload['formId'] == 14) {
    $res['data'] = [
        'formFields' => [
            [
                'id' => 59,
                'workOrderFormConfigId' => 14,
                'sort' => 0,
                'fieldName' => 'Order Number',
                'typeCode' => 'DepositOrderNo',
                'isRequired' => 1
            ],
            [
                'id' => 2142,
                'workOrderFormConfigId' => 14,
                'sort' => 1,
                'fieldName' => 'Deposit not received video/PDF',
                'typeCode' => 'FileUpload',
                'isRequired' => 0
            ],
            [
                'id' => 58,
                'workOrderFormConfigId' => 14,
                'sort' => 2,
                'fieldName' => 'Receiver UPI ID',
                'typeCode' => 'TextContent',
                'isRequired' => 1
            ],
            [
                'id' => 57,
                'workOrderFormConfigId' => 14,
                'sort' => 3,
                'fieldName' => 'UTR number',
                'typeCode' => 'UTR',
                'isRequired' => 0
            ],
            [
                'id' => 60,
                'workOrderFormConfigId' => 14,
                'sort' => 4,
                'fieldName' => 'Deposit proof receipt detail',
                'typeCode' => 'ImageUpload',
                'isRequired' => 0
            ],
            [
                'id' => 85,
                'workOrderFormConfigId' => 14,
                'sort' => 5,
                'fieldName' => 'OrderAmount ',
                'typeCode' => 'OrderAmount',
                'isRequired' => 1
            ],
            [
                'id' => 4413,
                'workOrderFormConfigId' => 14,
                'sort' => 6,
                'fieldName' => 'PDF Password',
                'typeCode' => 'TextContent',
                'isRequired' => 0
            ]
        ],
        'hasUserGuide' => 1,
        'displayName' => 'Deposit Not Receive'
    ];
} elseif ($payload['formId'] == 16) {
    $res['data'] = [
        'formFields' => [
            [
                'id' => 63,
                'workOrderFormConfigId' => 16,
                'sort' => 0,
                'fieldName' => 'Correct IFSC Code',
                'typeCode' => 'IFSC',
                'isRequired' => 1
            ],
            [
                'id' => 64,
                'workOrderFormConfigId' => 16,
                'sort' => 0,
                'fieldName' => 'Bank number',
                'typeCode' => 'BankAccountNumber',
                'isRequired' => 1
            ]
        ],
        'hasUserGuide' => 1,
        'displayName' => 'IFSC Modification'
    ];
  } elseif ($payload['formId'] == 18) {
    $res['data'] = [
        'formFields' => [
            [
                'id' => 71,
                'workOrderFormConfigId' => 18,
                'sort' => 0,
                'fieldName' => 'Old Passbook Bank',
                'typeCode' => 'ImageUpload',
                'isRequired' => 1
            ],
            [
                'id' => 69,
                'workOrderFormConfigId' => 18,
                'sort' => 0,
                'fieldName' => 'Bank account',
                'typeCode' => 'BankAccountNumber',
                'isRequired' => 1
            ],
            [
                'id' => 68,
                'workOrderFormConfigId' => 18,
                'sort' => 0,
                'fieldName' => 'Selfie holding your ID card',
                'typeCode' => 'ImageUpload',
                'isRequired' => 1
            ],
            [
                'id' => 70,
                'workOrderFormConfigId' => 18,
                'sort' => 0,
                'fieldName' => 'Latest Deposit Receipt Proof',
                'typeCode' => 'ImageUpload',
                'isRequired' => 1
            ]
        ],
        'hasUserGuide' => 1,
        'displayName' => 'Delete Withdraw Bank Account and Rebind'
    ];
} elseif ($payload['formId'] == 30) {
    // If `formId` is 30, provide the form fields for "Self-service Delete Bank Account and Rebind"
    $res['data'] = [
        'formFields' => [
            [
                'id' => 131,
                'workOrderFormConfigId' => 30,
                'sort' => 0,
                'fieldName' => 'Register phone number to receive OTP',
                'typeCode' => 'PhoneEmailCaptcha',
                'isRequired' => 1
            ]
        ],
        'hasUserGuide' => 1,
        'displayName' => 'Self-service Delete Bank Account and Rebind'
    ];
} elseif ($payload['formId'] == 15) {
    $res['data'] = [
        'formFields' => [
            [
                'id' => 61,
                'workOrderFormConfigId' => 15,
                'sort' => 0,
                'fieldName' => 'Correct bank name',
                'typeCode' => 'CardName',
                'isRequired' => 1
            ],
            [
                'id' => 62,
                'workOrderFormConfigId' => 15,
                'sort' => 0,
                'fieldName' => 'Bank number',
                'typeCode' => 'BankAccountNumber',
                'isRequired' => 1
            ]
        ],
        'hasUserGuide' => 1,
        'displayName' => 'Change bank name'
    ];
} elseif ($payload['formId'] == 31) {
    $res['data'] = [
        'formFields' => [
            [
                'id' => 133,
                'workOrderFormConfigId' => 31,
                'sort' => 0,
                'fieldName' => 'Register phone number to receive OTP',
                'typeCode' => 'PhoneEmailCaptcha',
                'isRequired' => 1
            ]
        ],
        'hasUserGuide' => 1,
        'displayName' => 'Self-service to Delete Old USDT Address and Rebind'
    ];
 } elseif ($payload['formId'] == 17) {
    $res['data'] = [
        'formFields' => [
            [
                'id' => 65,
                'workOrderFormConfigId' => 17,
                'sort' => 0,
                'fieldName' => 'Photo Selfie Holding Identity Card',
                'typeCode' => 'ImageUpload',
                'isRequired' => 1
            ],
            [
                'id' => 66,
                'workOrderFormConfigId' => 17,
                'sort' => 0,
                'fieldName' => 'Photo Selfie Hold USDT Address',
                'typeCode' => 'ImageUpload',
                'isRequired' => 1
            ],
            [
                'id' => 67,
                'workOrderFormConfigId' => 17,
                'sort' => 0,
                'fieldName' => 'Deposit Receipt Proof',
                'typeCode' => 'ImageUpload',
                'isRequired' => 1
            ]
        ],
        'hasUserGuide' => 1,
        'displayName' => 'Delete Old USDT Address and Rebind'
    ];
  } elseif ($payload['formId'] == 75) {
    $res['data'] = [
        'formFields' => [
            [
                'id' => 391,
                'workOrderFormConfigId' => 75,
                'sort' => 0,
                'fieldName' => 'Attach clear screenshot photo/video of the problem (optional)',
                'typeCode' => 'FileUpload',
                'isRequired' => 0
            ],
            [
                'id' => 390,
                'workOrderFormConfigId' => 75,
                'sort' => 0,
                'fieldName' => 'Explain the issue happen to you inside the game clear and detail',
                'typeCode' => 'LongText',
                'isRequired' => 1
            ]
        ],
        'hasUserGuide' => 0,
        'displayName' => 'Game Problems'
    ];
  } elseif ($payload['formId'] == 25) {
    $res['data'] = [
        'formFields' => [
            [
                'id' => 3638,
                'workOrderFormConfigId' => 25,
                'sort' => 0,
                'fieldName' => 'More than 3 days not receive, attach PDF bank statement',
                'typeCode' => 'TextContent',
                'isRequired' => 1
            ],
            [
                'id' => 3637,
                'workOrderFormConfigId' => 25,
                'sort' => 1,
                'fieldName' => 'Provide Video Record login Bank Account showing Bank Detail with bank statement.',
                'typeCode' => 'FileUpload',
                'isRequired' => 1
            ],
            [
                'id' => 3636,
                'workOrderFormConfigId' => 25,
                'sort' => 2,
                'fieldName' => 'Withdrawal amount',
                'typeCode' => 'WithdrawAmount',
                'isRequired' => 1
            ],
            [
                'id' => 3635,
                'workOrderFormConfigId' => 25,
                'sort' => 3,
                'fieldName' => 'Withdraw Order number',
                'typeCode' => 'WithdrawOrderNo',
                'isRequired' => 1
            ]
        ],
        'hasUserGuide' => 1,
        'displayName' => 'Withdrawal problem'
    ];
    $res['msgParameters'] = null;
    $res['code'] = 0;
    $res['msg'] = 'Succeed';
    $res['msgCode'] = 0;
} else {
    $res['data'] = [
        'error' => 'Invalid formId'
    ];
    $res['msg'] = 'Invalid formId';
    $res['code'] = 400;
}


http_response_code($res['code']);
echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
