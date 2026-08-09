<?php
    // Set response headers for JSON format
    header('Content-Type: application/json; charset=utf-8');
    header('Strict-Transport-Security: max-age=31536000');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Access-Control-Allow-Credentials: true');
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    header('Access-Control-Allow-Origin: ' . $origin);
    header('vary: Origin');
    
    // Set the current time zone
    date_default_timezone_set("Asia/Kolkata");
    $shnunc = date("Y-m-d H:i:s");
    
    // Define the response structure
    $res = [
    "data" => [
        ["bankId" => 9, "bankName" => "Axis Bank"],
        ["bankId" => 10, "bankName" => "Indian Bank"],
        ["bankId" => 11, "bankName" => "State Bank of India"],
        ["bankId" => 12, "bankName" => "Kotak Mahindra Bank"],
        ["bankId" => 13, "bankName" => "Canara Bank"],
        ["bankId" => 14, "bankName" => "ICICI Bank"],
        ["bankId" => 15, "bankName" => "Punjab National Bank"],
        ["bankId" => 16, "bankName" => "Bank of India"],
        ["bankId" => 17, "bankName" => "IDBI Bank"],
        ["bankId" => 18, "bankName" => "Standard Chartered Bank"],
        ["bankId" => 19, "bankName" => "Karnataka Bank"],
        ["bankId" => 20, "bankName" => "HDFC Bank"],
        ["bankId" => 21, "bankName" => "Yes Bank"],
        ["bankId" => 22, "bankName" => "Central Bank of India"],
        ["bankId" => 23, "bankName" => "Union Bank of India"],
        ["bankId" => 24, "bankName" => "Bank of Baroda"],
        ["bankId" => 25, "bankName" => "FEDERAL BANK"],
        ["bankId" => 26, "bankName" => "Syndicate Bank"],
        ["bankId" => 27, "bankName" => "Bank of Maharashtra"],
        ["bankId" => 28, "bankName" => "Citibank India"],
        ["bankId" => 29, "bankName" => "Indian Overseas Bank"],
        ["bankId" => 30, "bankName" => "IDFC FIRST BANK LTD"],
        ["bankId" => 31, "bankName" => "Bandhan Bank"],
        ["bankId" => 32, "bankName" => "Indusind Bank"],
        ["bankId" => 33, "bankName" => "Jammu and Kashmir Bank Limited"],
        ["bankId" => 35, "bankName" => "Equitas Small Finance Bank Limited"],
        ["bankId" => 36, "bankName" => "India Post Payments Bank"],
        ["bankId" => 37, "bankName" => "Corporation Bank"],
        ["bankId" => 38, "bankName" => "City Union Bank"],
        ["bankId" => 39, "bankName" => "Karur Vysya Bank"],
        ["bankId" => 40, "bankName" => "Tamilnad Mercantile Bank"],
        ["bankId" => 41, "bankName" => "Allahabad Bank"],
        ["bankId" => 42, "bankName" => "Varachha Co-operative Bank"],
        ["bankId" => 43, "bankName" => "Meghalaya Rural Bank"],
        ["bankId" => 44, "bankName" => "AU Small Finance Bank"],
        ["bankId" => 45, "bankName" => "Lakshmi Vilas Bank"],
        ["bankId" => 46, "bankName" => "South Indian Bank"],
        ["bankId" => 47, "bankName" => "Bassein Catholic Cooperative Bank"],
        ["bankId" => 48, "bankName" => "Airtel Payments Bank Limited"],
        ["bankId" => 49, "bankName" => "State Bank of Hyderabad"],
        ["bankId" => 50, "bankName" => "Gp Parsik Bank"],
        ["bankId" => 51, "bankName" => "Kerala Gramin Bank"],
        ["bankId" => 52, "bankName" => "RBL Bank"],
        ["bankId" => 53, "bankName" => "Dhanlaxmi Bank"],
        ["bankId" => 54, "bankName" => "TJSB Bank"],
        ["bankId" => 55, "bankName" => "Punjab & Sind Bank"],
        ["bankId" => 56, "bankName" => "Purvanchal Bank"],
        ["bankId" => 57, "bankName" => "Sarva Haryana Gramin Bank"],
        ["bankId" => 58, "bankName" => "Ahmedabad District Co-Operative Bank"],
        ["bankId" => 59, "bankName" => "Fino Payments Bank"],
        ["bankId" => 60, "bankName" => "Saraswat Cooperative Bank"],
        ["bankId" => 63, "bankName" => "Andhra Pragathi Grameena Bank"],
        ["bankId" => 64, "bankName" => "Rajasthan Marudhara Gramin Bank"],
        ["bankId" => 65, "bankName" => "Abhyudaya Bank"],
        ["bankId" => 66, "bankName" => "Ujjivan Small Finance Bank"],
        ["bankId" => 68, "bankName" => "Telangana Grameena Bank"],
        ["bankId" => 69, "bankName" => "Capital Small Finance Bank"],
        ["bankId" => 70, "bankName" => "Mizoram Rural Bank"],
        ["bankId" => 71, "bankName" => "Andhra Pradesh Grameena Vikas Bank"],
        ["bankId" => 72, "bankName" => "Karnataka Vikas Grameena Bank"],
        ["bankId" => 73, "bankName" => "The Ahmedabad Merchantile Co-op Bank Ltd"],
        ["bankId" => 74, "bankName" => "Madhya Bihar Gramin Bank"],
        ["bankId" => 75, "bankName" => "NSDL Payments Bank"],
        ["bankId" => 76, "bankName" => "ESAF Small Finance Bank"],
        ["bankId" => 77, "bankName" => "Himachal Pradesh State Cooperative Bank"],
        ["bankId" => 78, "bankName" => "Maharashtra State Cooperative Bank"],
        ["bankId" => 79, "bankName" => "Oriental Bank of Commerce"],
        ["bankId" => 80, "bankName" => "Nainital Bank"],
        ["bankId" => 81, "bankName" => "Jharkhand Rajya Gramin Bank"],
        ["bankId" => 82, "bankName" => "Jio Payments Bank"],
        ["bankId" => 83, "bankName" => "Maharashtra Gramin Bank"],
        ["bankId" => 85, "bankName" => "Uttarakhand Gramin Bank"],
        ["bankId" => 88, "bankName" => "Himachal Pradesh Gramin Bank"],
        ["bankId" => 89, "bankName" => "Krishna District Co-Operative Central Bank Ltd."],
        ["bankId" => 90, "bankName" => "Rajkot Nagarik Sahakari Bank Ltd"],
        ["bankId" => 91, "bankName" => "North East Small Financial Bank"],
        ["bankId" => 92, "bankName" => "Catholic Syrian Bank"],
        ["bankId" => 93, "bankName" => "Fincare Small Finance Bank"],
        ["bankId" => 94, "bankName" => "Baroda Uttar Pradesh Gramin Bank"],
        ["bankId" => 95, "bankName" => "Dhanalakshmi Bank"],
        ["bankId" => 96, "bankName" => "Cosmos Co-operative Bank Ltd"],
        ["bankId" => 97, "bankName" => "Saurashtra Gramin Bank"],
        ["bankId" => 98, "bankName" => "Baroda Rajasthan Kshetriya Gramin Bank"],
        ["bankId" => 100, "bankName" => "Jana Small Finance Bank"],
        ["bankId" => 102, "bankName" => "Dena Gujarat Gramin Bank"],
        ["bankId" => 103, "bankName" => "Chaitanya Godavari Grameena Bank"],
        ["bankId" => 104, "bankName" => "SVC Bank"],
        ["bankId" => 105, "bankName" => "Bharat Cooperative Bank"],
        ["bankId" => 106, "bankName" => "The Surat District Co-Op. Bank Ltd"],
        ["bankId" => 107, "bankName" => "USDT"],
        ["bankId" => 108, "bankName" => "The Kalupur Commercial Co-operative Bank"],
        ["bankId" => 109, "bankName" => "Prime Co-operative Bank"],
        ["bankId" => 110, "bankName" => "Tripura Gramin Bank"],
        ["bankId" => 111, "bankName" => "Zila Sahakari Bank Ltd Bareilly"],
        ["bankId" => 112, "bankName" => "Aryavart Bank"],
        ["bankId" => 113, "bankName" => "Development Credit Bank"],
        ["bankId" => 114, "bankName" => "Sarva UP Gramin Bank"],
        ["bankId" => 115, "bankName" => "Laxmi Co-operative Bank"],
        ["bankId" => 116, "bankName" => "Idukki District Co-Operative Bank"],
        ["bankId" => 117, "bankName" => "The Goa State Co-op. Bank"],
        ["bankId" => 118, "bankName" => "Indian Bank"],
        ["bankId" => 119, "bankName" => "Suryoday Small Finance Bank"],
        ["bankId" => 120, "bankName" => "UCO Bank"],
    ]
];

                    $res['code'] = 0;
                    $res['msg'] = 'Succeed';
                    $res['msgCode'] = 0;
                    http_response_code(200);
                    $res['code'] = 0;
                    $res['msg'] = 'Succeed';
                    $res['msgCode'] = 0;
                    http_response_code(200);
    
    // Return the response in JSON format
  
    echo json_encode($res);
?>
