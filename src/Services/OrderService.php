<?php
namespace App\Services;

use App\Api\PayPal;
use App\Db\MySQL;
use App\Mail\Mailer;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;

class OrderService {

    private $connection;
    private $paypal;
    private $mailer;

    public function __construct() {
        $this->connection = MySQL::getInstance()->connection;
        $this->paypal = PayPal::getInstance();
        $this->mailer = Mailer::getInstance();
    }

    public function processOrder($name, $email) {
        $orderItems = [
            [
                "name_product" => "Скрепки",
                "price" => 20,
                "qty" => 2,
            ],
            [
                "name_product" => "Шариковая ручка",
                "price" => 55.5,
                "qty" => 5,
            ]
        ];
        $totalQty = 0;
        $totalSum = 0.0;
        $uuid = $this->generateUUID();

        foreach ($orderItems as $item) {
            $totalQty += $item['qty'];
            $totalSum += $item['price'] * $item['qty'];
        }
        
        try {
            $this->connection->beginTransaction();
            $stmt = $this->connection->prepare("INSERT INTO orders (id, name, email, qty, sum, currency, created_at) VALUES (:id, :name, :email, :qty, :sum, 'USD', NOW())");
            $stmt->bindParam(':id', $uuid);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':qty', $totalQty);
            $stmt->bindParam(':sum', $totalSum);
            $res = $stmt->execute();
            $stmt = $this->connection->prepare("INSERT INTO order_items (id, id_order, name_product, price, qty) VALUES (UUID(), :id_order, :name_product, :price, :qty)");
            foreach ($orderItems as $item) {
                $stmt->bindParam(':id_order', $uuid);
                $stmt->bindParam(':name_product', $item['name_product']);
                $stmt->bindParam(':price', $item['price']);
                $stmt->bindParam(':qty', $item['qty']);
                $stmt->execute();
            }
            $this->connection->commit();
        } catch (PDOException $e) {
            $this->connection->rollBack();
            die("Возникла ошибка: " . $e->getMessage());
        }


        $this->mailer->sendOrderEmails($email, $name, $uuid, 'pending');


        $protocol = isset($_SERVER['HTTPS']) && 
        $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $base_url = $protocol . $_SERVER['HTTP_HOST'];

        $returnUrl = $base_url.'/paypal_callback?success=true&order_id=' . $uuid;
        $cancelUrl = $base_url.'/paypal_callback?success=false&order_id=' . $uuid;
        $response = $this->paypal->createOrder($totalSum, 'USD', $returnUrl, $cancelUrl);
        if (isset($response['id'])) {
            $token = $response['id'];

            $stmt = $this->connection->prepare("UPDATE orders SET token = ? WHERE id = ?");
            $stmt->execute([$token, $uuid]);

            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    header('Location: ' . $link['href']);
                    exit();
                }
            }
        }
        else { 
            die("Возникла ошибка");
        }

    }
    private function generateUUID($data = null) {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
