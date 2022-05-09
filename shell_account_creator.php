<?php

require_once 'vendor/autoload.php';
require_once 'simple_html_dom.php';
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class Shell 
{
    public function create_device($length)
        {
            $data = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $string = '';
            for($i = 0; $i < $length; $i++) {
                $pos = rand(0, strlen($data)-1);
                $string .= $data{$pos};
            }
            return $string;
        } 

    public function getInfoUser()
    {
        $client = new Client();
        $crawel = $client->request('GET', 'https://name-fake.com/id_ID');
        $firstName = $crawel->filter('#copy1')->text();
        $lastName = $crawel->filter('#copy2')->text();
        $email = $crawel->filter('#copy4')->text();
        $username = $crawel->filter('#copy3')->text();
        $password = $crawel->filter('#copy5')->text();
        $date = $crawel->filter('#copy12')->text();
        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'date' => $date

        ];
     
    }

    public function generateTokenOtp($no, $deviceId)
    {
        // $deviceId = $this->create_device(22);

        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://apac2-auth-api.capillarytech.com/auth/v1/token/generate",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\n\t\"brand\":\"SHELLINDONESIALIVE\",\n\t\"deviceId\":\"$deviceId\",\n\t\"mobile\":\"62$no\"\n}",
        CURLOPT_HTTPHEADER => [
            "Cap_device_id: $deviceId",
            "Cap_mobile: 62$no",
            "Content-Type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $json = json_decode($response, true);
        return $json;
    } 


    public function sendOtp($no, $deviceId, $session)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://apac2-auth-api.capillarytech.com/auth/v1/otp/generate",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\n\t\"brand\":\"SHELLINDONESIALIVE\",\n\t\"deviceId\":\"$deviceId\",\n\t\"mobile\":\"62$no\",\n\t\"mobile_temp\":\"+62 89509757249\",\n\t\"sessionId\":\"$session\"\n}",
        CURLOPT_HTTPHEADER => [
            "Cap_device_id: $deviceId",
            "Cap_mobile: $no",
            "Content-Type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $json = json_decode($response, true);
        return $json;
    }

    public function validateOtp($no, $deviceId, $session, $otp)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_URL => "https://apac2-auth-api.capillarytech.com/auth/v1/otp/validate",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\n\t\"brand\":\"SHELLINDONESIALIVE\",\n\t\"deviceId\":\"$deviceId\",\n\t\"mobile\":\"62$no\",\n\t\"mobile_temp\":\"+62 $no\",\n\t\"otp\":\"$otp\",\n\t\"sessionId\":\"$session\"\n}",
          CURLOPT_HTTPHEADER => [
            "Cap_device_id: $deviceId",
            "Cap_mobile: 62$no",
            "Content-Type: application/json"
          ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $json = json_decode($response, true);
        return $json;
    }

    public function setInfoUser($no, $deviceId, $date, $firstName, $lastName, $email, $token)
    {
        $curl = curl_init();
            curl_setopt_array($curl, [
            CURLOPT_URL => "https://apac2-api-gateway.capillarytech.com/mobile/v2/api/v2/customers",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"extendedFields\":\n {\n\t\"acquisition_channel\":\"mobileApp\",\n\t \"dob\":\"$date\",\n \"verification_status\":\"false\"\n},\n \"loyaltyInfo\":{\n\t \"loyaltyType\":\"loyalty\"\n },\n \"profiles\":[\n\t {\"fields\":{\n\t\t \"onboarding\":\"pending\"\n\t }\n\t\t,\n\t\t\"firstName\":\"$firstName\"\n\t\t,\"identifiers\":[\n\t\t\t{\"type\":\"mobile\",\n\t\t\t \"value\":\"62$no\"\n\t\t\t},\n\t\t\t{\n\t\t\t\t\"type\":\"email\",\n\t\t\t\t\"value\":\"$email\"\n\t\t\t}\n\t\t],\n\t\t\"lastName\":\"$lastName\"\n\t }\n ],\n \"statusLabel\":\"Active\"\n ,\"statusLabelReason\":\"App Registration\"\n}",
            CURLOPT_HTTPHEADER => [
                "Cap_authorization: $token",
                "Cap_brand: SHELLINDONESIALIVE",
                "Cap_device_id: $deviceId",
                "Cap_mobile: 62$no",
                "Content-Type: application/json"
            ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
    }

    public function cekInfoUserSuccess($no, $deviceId, $token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://apac2-api-gateway.capillarytech.com/mobile/v2/api/customer/update",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\n\t\"root\": {\"customer\":[\n\t\t\t\t\t\t {\"custom_fields\":{\n\t\t\t\t\t\t \"field\":[\n\t\t\t\t\t\t\t {\"name\":\"onboarding\",\n\t\t\t\t\t\t\t\t\t\t\t \"value\":\"completed\"}\n\t\t\t\t\t\t ]},\n\t\t\t\t\t\t\t\"extended_fields\":{\n\t\t\t\t\t\t\t\t\"field\":[\n\t\t\t\t\t\t\t\t\t{\"name\":\"vehicle_type\",\n\t\t\t\t\t\t\t\t\t\t\t\t\t\"value\":\"2W\"}\n\t\t\t\t\t\t\t\t]},\n\t\t\t\t\t\t\t\"loyalty_points\":0,\n\t\t\t\t\t\t\t\"mobile\":\"62$no\"\n\t\t\t\t\t\t }]\n\t\t\t\t\t}\n}",
        CURLOPT_HTTPHEADER => [
            "Cap_authorization: $token",
            "Cap_brand: SHELLINDONESIALIVE",
            "Cap_device_id: $deviceId",
            "Cap_mobile: 62$no",
            "Content-Type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $json = json_decode($response, true);
        return $json;
    }
}

$botShell = new Shell;

$deviceId = $botShell->create_device(22);

echo "
╭━━━┳╮╱╱╱╱╭╮╭╮╱╭━━━╮╱╱╱╱╱╱╱╱╱╱╱╭━━━╮
┃╭━╮┃┃╱╱╱╱┃┃┃┃╱┃╭━╮┃╱╱╱╱╱╱╱╱╱╱╱┃╭━━╯
┃╰━━┫╰━┳━━┫┃┃┃╱┃┃╱┃┣━━┳┳━━╮╱╱╱╱┃╰━━┳━━┳━━┳━━┳━┳━━╮
╰━━╮┃╭╮┃┃━┫┃┃┃╱┃╰━╯┃━━╋┫╭╮┃╭━━╮┃╭━━┫┃━┫╭━┫╭╮┃╭┫┃━┫
┃╰━╯┃┃┃┃┃━┫╰┫╰╮┃╭━╮┣━━┃┃╭╮┃╰━━╯┃┃╱╱┃┃━┫╰━┫╰╯┃┃┃┃━┫
╰━━━┻╯╰┻━━┻━┻━╯╰╯╱╰┻━━┻┻╯╰╯╱╱╱╱╰╯╱╱╰━━┻━━┻━━┻╯╰━━╯ " . PHP_EOL;
echo "\n";
echo  "\033[96m*WAJIB MENGGUNAKAN SIMCARD FISIK! \033[0m" . PHP_EOL;
echo "\n";
echo "\033[92mInput Nomor (Contoh 81319999999) : \033[0m";
$nomor = trim(fgets(STDIN));
"\n";
$sessionIdForGenerateOtp = $botShell->generateTokenOtp($nomor, $deviceId);

    if($sessionIdForGenerateOtp['status']['success'] == true){
        echo "\033[93mBerhasil Mendapatkan Session Id Untuk OTP\033[0m" . PHP_EOL;
    }elseif($sessionIdForGenerateOtp['success'] == false){
        echo "\033[31mTerjadi Masalah, Program Berakhir! \033[0m" . PHP_EOL;
        exit;
    }


$generateNewOtp = $botShell->sendOtp($nomor, $deviceId, $sessionIdForGenerateOtp['user']['sessionId']); //kirim otp
    if($generateNewOtp['status']['success'] == true){
        echo "\033[93mBerhasil Mengirim OTP via SMS\033[0m" . PHP_EOL;
    }else{
        echo "\033[91mTerjadi Masalah, Program Berakhir! \033[0m" . PHP_EOL;
        exit;
    }

    otp:
    echo "\033[92mInput OTP : \033[0m";
    $otp = trim(fgets(STDIN));
    "\n";
    $resultValidateOtp = $botShell->validateOtp($nomor, $deviceId, $sessionIdForGenerateOtp['user']['sessionId'], $otp);
    if($resultValidateOtp['status']['success'] == true){
        $token = $resultValidateOtp['auth']['token'];
        $firstName = $botShell->getInfoUser()['firstName'];
        $lastName = $botShell->getInfoUser()['lastName'];
        $email = $botShell->getInfoUser()['email'];
        $date=date_create($botShell->getInfoUser()['date']);
        $formmatDate = date_format($date,"Y/m/d");
        $setInfoAcc = $botShell->setInfoUser($nomor, $deviceId, $formmatDate, $firstName, $lastName, $email, $token);

        $cekInfoAcc = $botShell->cekInfoUserSuccess($nomor, $deviceId, $token);
        $firstNameRess = $cekInfoAcc['customers']['customer'][0]['firstname'];
        $lastNameRess = $cekInfoAcc['customers']['customer'][0]['lastname'];
        $phone = $cekInfoAcc['customers']['customer'][0]['mobile'];
        $ressEmail = $cekInfoAcc['customers']['customer'][0]['email'];
        $point = $cekInfoAcc['customers']['customer'][0]['loyalty_points'];

         echo "\033[92mFirst Name : $firstNameRess \033[0m" . PHP_EOL;
         echo "\033[92mLast Name : $lastNameRess \033[0m" . PHP_EOL;
         echo "\033[92mPhone : $phone \033[0m" . PHP_EOL;
         echo "\033[92mEmail : $ressEmail \033[0m" . PHP_EOL;
         echo "\033[92mPOINT : $point \033[0m" . PHP_EOL;
        $fileTxt = fopen("acc.txt", "a");
        $resultData = "\nFirst Name = $firstNameRess\nLast Name = $lastNameRess\nPhone = $phone\nEmail = $ressEmail\nPoint = $point\n=============================================";
        $resultSave = fwrite($fileTxt, $resultData);
        fclose($fileTxt);
        

    }else{
        echo "\033[91mERROR / OTP SALAH. \033[0m" . PHP_EOL;
        goto otp;
    }

