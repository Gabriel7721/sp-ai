<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Electronics
            [
                'sku' => 'ELX-IPH15-128-BLK',
                'name' => 'iPhone 15 128GB Black',
                'brand' => 'Apple',
                'category' => 'Electronics',
                'description' => 'Smartphone A16 Bionic, camera 48MP, USB-C, màn hình Super Retina XDR 6.1".',
                'price' => 799.00,
                'currency' => 'USD',
                'stock' => 35,
                'url' => 'https://shop.example.com/products/iphone-15-128gb-black',
                'attributes' => ['storage' => '128GB', 'color' => 'Black', 'warranty' => '12 months']
            ],
            [
                'sku' => 'ELX-PIX8-128-OBL',
                'name' => 'Google Pixel 8 128GB Obsidian',
                'brand' => 'Google',
                'category' => 'Electronics',
                'description' => 'Pixel 8 với Google Tensor G3, camera AI mạnh, 7 năm cập nhật phần mềm.',
                'price' => 699.00,
                'currency' => 'USD',
                'stock' => 20,
                'url' => 'https://shop.example.com/products/google-pixel-8-128gb-obsidian',
                'attributes' => ['storage' => '128GB', 'color' => 'Obsidian']
            ],
            [
                'sku' => 'ELX-S23U-256-CRM',
                'name' => 'Samsung Galaxy S23 Ultra 256GB Cream',
                'brand' => 'Samsung',
                'category' => 'Electronics',
                'description' => 'S23 Ultra với S Pen, camera 200MP, Snapdragon 8 Gen 2 for Galaxy.',
                'price' => 1199.00,
                'currency' => 'USD',
                'stock' => 12,
                'url' => 'https://shop.example.com/products/galaxy-s23-ultra-256gb-cream',
                'attributes' => ['storage' => '256GB', 'color' => 'Cream', 'pen' => true]
            ],
            [
                'sku' => 'ELX-SNY-WH1000XM5-BLK',
                'name' => 'Sony WH-1000XM5 Noise Cancelling Headphones',
                'brand' => 'Sony',
                'category' => 'Electronics',
                'description' => 'Tai nghe chống ồn hàng đầu, âm thanh chi tiết, pin tới 30 giờ.',
                'price' => 399.99,
                'currency' => 'USD',
                'stock' => 40,
                'url' => 'https://shop.example.com/products/sony-wh-1000xm5',
                'attributes' => ['wireless' => true, 'anc' => true]
            ],
            [
                'sku' => 'ELX-ANK-SMOVE-2',
                'name' => 'Anker Soundcore Motion+ Portable Speaker',
                'brand' => 'Anker',
                'category' => 'Electronics',
                'description' => 'Loa di động Hi-Res 30W, Bluetooth 5.0, chống nước IPX7.',
                'price' => 119.99,
                'currency' => 'USD',
                'stock' => 50,
                'url' => 'https://shop.example.com/products/anker-soundcore-motion-plus',
                'attributes' => ['waterproof' => 'IPX7']
            ],

            // Kitchen
            [
                'sku' => 'KTC-PSL-AIRFRY-5L',
                'name' => 'Philips Airfryer 5L Essential',
                'brand' => 'Philips',
                'category' => 'Kitchen',
                'description' => 'Nồi chiên không dầu 5 lít, Rapid Air, tiết kiệm dầu 90%.',
                'price' => 169.00,
                'currency' => 'USD',
                'stock' => 27,
                'url' => 'https://shop.example.com/products/philips-airfryer-5l',
                'attributes' => ['capacity' => '5L', 'watt' => '1400W']
            ],
            [
                'sku' => 'KTC-ZOJ-NS-TSC10',
                'name' => 'Zojirushi Micom Rice Cooker 5.5-cup',
                'brand' => 'Zojirushi',
                'category' => 'Kitchen',
                'description' => 'Nồi cơm điện Micom, giữ ấm kéo dài, menu gạo Jasmin/Sushi.',
                'price' => 179.95,
                'currency' => 'USD',
                'stock' => 18,
                'url' => 'https://shop.example.com/products/zojirushi-micom-5-5-cup',
                'attributes' => ['capacity' => '1.0L', 'keep_warm' => true]
            ],
            [
                'sku' => 'KTC-LEC-OVEN-26CM',
                'name' => 'Le Creuset Enameled Cast Iron Dutch Oven 26cm',
                'brand' => 'Le Creuset',
                'category' => 'Kitchen',
                'description' => 'Nồi gang tráng men 26cm, giữ nhiệt tốt, nấu hầm/soup.',
                'price' => 369.00,
                'currency' => 'USD',
                'stock' => 10,
                'url' => 'https://shop.example.com/products/le-creuset-dutch-oven-26cm',
                'attributes' => ['diameter' => '26cm', 'material' => 'cast iron']
            ],
            [
                'sku' => 'KTC-BRV-BRV-700BL',
                'name' => 'Breville Barista Express Espresso Machine',
                'brand' => 'Breville',
                'category' => 'Kitchen',
                'description' => 'Máy pha espresso với grinder tích hợp, kiểm soát nhiệt PID.',
                'price' => 699.95,
                'currency' => 'USD',
                'stock' => 6,
                'url' => 'https://shop.example.com/products/breville-barista-express',
                'attributes' => ['grinder' => true, 'pressure' => '15 bar']
            ],
            [
                'sku' => 'KTC-LODGE-SK12',
                'name' => 'Lodge Cast Iron Skillet 12-inch',
                'brand' => 'Lodge',
                'category' => 'Kitchen',
                'description' => 'Chảo gang 12", bền bỉ, giữ nhiệt tốt cho áp chảo steak.',
                'price' => 39.90,
                'currency' => 'USD',
                'stock' => 80,
                'url' => 'https://shop.example.com/products/lodge-skillet-12',
                'attributes' => ['diameter' => '12 inch', 'material' => 'cast iron']
            ],

            // Books
            [
                'sku' => 'BOK-CLN-CLEAN-CODE',
                'name' => 'Clean Code',
                'brand' => 'Pearson',
                'category' => 'Books',
                'description' => 'Robert C. Martin: nguyên tắc viết code sạch, dễ bảo trì.',
                'price' => 32.00,
                'currency' => 'USD',
                'stock' => 120,
                'url' => 'https://shop.example.com/products/clean-code',
                'attributes' => ['author' => 'Robert C. Martin', 'year' => 2008]
            ],
            [
                'sku' => 'BOK-DSA-GROK-ALG',
                'name' => 'Grokking Algorithms',
                'brand' => 'Manning',
                'category' => 'Books',
                'description' => 'Giải thích thuật toán qua hình minh họa, dễ hiểu cho người mới.',
                'price' => 40.00,
                'currency' => 'USD',
                'stock' => 90,
                'url' => 'https://shop.example.com/products/grokking-algorithms',
                'attributes' => ['author' => 'Aditya Bhargava', 'year' => 2016]
            ],
            [
                'sku' => 'BOK-LRV-LARAVEL-UP',
                'name' => 'Laravel: Up & Running (3rd)',
                'brand' => "O'Reilly",
                'category' => 'Books',
                'description' => 'Hướng dẫn Laravel hiện đại, thực tiễn cho dự án production.',
                'price' => 49.99,
                'currency' => 'USD',
                'stock' => 70,
                'url' => 'https://shop.example.com/products/laravel-up-and-running',
                'attributes' => ['author' => 'Matt Stauffer', 'year' => 2023]
            ],
            [
                'sku' => 'BOK-SRE-GOOGLE',
                'name' => 'Site Reliability Engineering',
                'brand' => "O'Reilly",
                'category' => 'Books',
                'description' => 'Tư duy SRE của Google: độ tin cậy, vận hành, SLIs/SLOs.',
                'price' => 55.00,
                'currency' => 'USD',
                'stock' => 60,
                'url' => 'https://shop.example.com/products/site-reliability-engineering',
                'attributes' => ['author' => 'Beyer et al.', 'year' => 2016]
            ],
            [
                'sku' => 'BOK-DS-PRAC',
                'name' => 'Designing Data-Intensive Applications',
                'brand' => "O'Reilly",
                'category' => 'Books',
                'description' => 'Martin Kleppmann: hệ thống dữ liệu phân tán, kiến trúc bền vững.',
                'price' => 59.99,
                'currency' => 'USD',
                'stock' => 45,
                'url' => 'https://shop.example.com/products/designing-data-intensive-applications',
                'attributes' => ['author' => 'Martin Kleppmann', 'year' => 2017]
            ],
            [
                'sku' => 'BOK-PRJ-MYSQL',
                'name' => 'High Performance MySQL',
                'brand' => "O'Reilly",
                'category' => 'Books',
                'description' => 'Tối ưu hóa MySQL: indexing, replication, sharding.',
                'price' => 64.00,
                'currency' => 'USD',
                'stock' => 35,
                'url' => 'https://shop.example.com/products/high-performance-mysql',
                'attributes' => ['author' => 'Baron Schwartz', 'year' => 2012]
            ],
            [
                'sku' => 'BOK-UI-DESIGN',
                'name' => 'Refactoring UI',
                'brand' => 'Independent',
                'category' => 'Books',
                'description' => 'Thiết kế UI thực tiễn cho dev: typography, spacing, màu sắc.',
                'price' => 99.00,
                'currency' => 'USD',
                'stock' => 22,
                'url' => 'https://shop.example.com/products/refactoring-ui',
                'attributes' => ['author' => 'Adam Wathan, Steve Schoger', 'year' => 2018]
            ],
            [
                'sku' => 'BOK-SYS-DESIGN-INTERVIEW',
                'name' => 'System Design Interview Vol.1',
                'brand' => 'Independent',
                'category' => 'Books',
                'description' => 'Hệ thống lớn: load balancer, cache, queue, database scale.',
                'price' => 49.00,
                'currency' => 'USD',
                'stock' => 55,
                'url' => 'https://shop.example.com/products/system-design-interview-v1',
                'attributes' => ['author' => 'Alex Xu', 'year' => 2020]
            ],
            [
                'sku' => 'BOK-SYS-DESIGN-V2',
                'name' => 'System Design Interview Vol.2',
                'brand' => 'Independent',
                'category' => 'Books',
                'description' => 'Tiếp nối phần 1 với case study nâng cao, trade-offs thực tế.',
                'price' => 59.00,
                'currency' => 'USD',
                'stock' => 40,
                'url' => 'https://shop.example.com/products/system-design-interview-v2',
                'attributes' => ['author' => 'Alex Xu', 'year' => 2022]
            ],
            [
                'sku' => 'BOK-PHP-ARCH',
                'name' => 'PHP Architect’s Guide to PHP',
                'brand' => 'php[architect]',
                'category' => 'Books',
                'description' => 'Các mẫu thiết kế, testing, performance trong PHP hiện đại.',
                'price' => 44.00,
                'currency' => 'USD',
                'stock' => 30,
                'url' => 'https://shop.example.com/products/php-architect-guide',
                'attributes' => ['author' => 'php[architect]', 'year' => 2021]
            ],
        ];

        foreach ($items as $it) {
            Product::create($it);
        }
    }
}
