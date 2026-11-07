# Hướng dẫn chạy WordPress bằng Laragon hoặc XAMPP

Tài liệu này thay thế phần Docker khi chỉ cần chạy PHP, MySQL và WordPress trên Windows. Quy trình push GitHub và deploy hosting vẫn giữ nguyên.

## 1. Chọn công cụ

Khuyến nghị dùng Laragon vì dễ cài, dễ đổi phiên bản PHP và tự tạo domain local.

- Laragon: https://laragon.org/download/
- XAMPP: https://www.apachefriends.org/download.html

Không cần cài cả hai. Chỉ chọn một công cụ.

## 2. Clone source trực tiếp vào Laragon hoặc XAMPP

Không cần copy source WordPress thủ công. Clone toàn bộ repository GitHub trực tiếp vào thư mục web của công cụ bạn chọn.

Thay URL bằng repository của từng site:

~~~text
https://github.com/USERNAME/REPOSITORY.git
~~~

Trong PowerShell, chọn một trong hai thư mục sau.

Laragon:

~~~powershell
cd C:\laragon\www
git clone https://github.com/USERNAME/REPOSITORY.git PROJECT_NAME
~~~

XAMPP:

~~~powershell
cd C:\xampp\htdocs
git clone https://github.com/USERNAME/REPOSITORY.git PROJECT_NAME
~~~

Sau khi clone, cấu trúc phải là:

~~~text
PROJECT_NAME\
└── wordpress\
    ├── wp-admin\
    ├── wp-content\
    └── wp-includes\
~~~

Luôn chạy lệnh git tại thư mục gốc PROJECT_NAME, không chạy bên trong riêng thư mục wordpress.

## 3. Chạy bằng Laragon

### Cài và khởi động

1. Cài Laragon.
2. Mở Laragon.
3. Chọn Start All.
4. Đảm bảo Apache và MySQL đang chạy.

### Cấu hình Document Root

Repository đã nằm tại C:\laragon\www\PROJECT_NAME. Vì source WordPress nằm trong thư mục con, cấu hình Document Root của Apache trỏ vào:

C:\laragon\www\PROJECT_NAME\wordpress

Sau đó truy cập:

http://PROJECT_NAME.test

Nếu chưa cấu hình Document Root, truy cập tạm:

http://PROJECT_NAME.test/wordpress

### Tạo database

Mở Database hoặc phpMyAdmin trong Laragon và tạo database:

~~~text
wordpress
~~~

Thông tin mặc định thường dùng:

~~~text
Database host: 127.0.0.1
Database name: wordpress
Database user: root
Database password: để trống nếu chưa đặt
Database port: 3306
~~~

Nếu đã đặt mật khẩu MySQL, dùng mật khẩu thực tế.

## 4. Chạy bằng XAMPP

### Cài và khởi động

1. Cài XAMPP.
2. Mở XAMPP Control Panel.
3. Bấm Start cho Apache và MySQL.

### Cấu hình Document Root

Repository đã nằm tại C:\xampp\htdocs\PROJECT_NAME. Cấu hình Apache Virtual Host hoặc Document Root trỏ vào:

C:\xampp\htdocs\PROJECT_NAME\wordpress

Sau đó truy cập domain local đã cấu hình.

Nếu chưa cấu hình Virtual Host, truy cập tạm:

http://localhost/PROJECT_NAME/wordpress

### Tạo database

Mở:

http://localhost/phpmyadmin

Tạo database tên wordpress. Thông tin mặc định:

~~~text
Database host: 127.0.0.1
Database name: wordpress
Database user: root
Database password: để trống
Database port: 3306
~~~

Nếu MySQL có mật khẩu, cập nhật đúng mật khẩu trong file wp-config.php.

## 5. Cấu hình WordPress

Nếu chưa có file cấu hình, copy:

wordpress/wp-config-sample.php

thành:

wordpress/wp-config.php

Các giá trị local thường là:

~~~php
define('DB_NAME', 'wordpress');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', '127.0.0.1');
~~~

File wp-config.php không được commit lên GitHub.

Nếu có file SQL backup, import trong phpMyAdmin bằng chức năng Import. Sau đó kiểm tra bảng wp_options và cập nhật siteurl, home cho đúng URL local.

## 6. Chỉnh sửa source PHP

Mở thư mục sau bằng VS Code:

wordpress/wp-content/themes/seooneclick/

Có thể chỉnh các file:

~~~text
style.css
functions.php
index.php
header.php
footer.php
single.php
page.php
~~~

Sau khi lưu, tải lại website local.

Xem lỗi PHP:

- Laragon: Menu → Apache → Apache error log.
- XAMPP: C:\xampp\apache\logs\error.log

## 7. Commit và push GitHub

Thực hiện tại thư mục gốc repository:

~~~powershell
cd C:\duong-dan\isurface
git status
git add wordpress
git commit -m "update wordpress source"
git push origin main
~~~

Không commit các file:

~~~text
.env
wordpress/wp-config.php
wordpress/wp-content/uploads/
wordpress/wp-content/cache/
~~~

## 8. GitHub tự deploy lên hosting

Sau khi push vào branch main, workflow:

.github/workflows/deploy.yml

sẽ:

1. Checkout source.
2. Tạo SSH key tạm từ DEPLOY_SSH_KEY.
3. Kết nối đến host bằng SSH.
4. Đồng bộ thư mục wordpress/.
5. Đưa source vào thư mục deploy đã cấu hình trên hosting.

Xem kết quả tại:

GitHub repository → Actions → Deploy WordPress to cPanel

Nếu job màu xanh, kiểm tra:

https://YOUR_DOMAIN.com

## 9. GitHub Secrets

Tại Settings → Secrets and variables → Actions, tạo Repository secrets:

| Tên | Giá trị |
|---|---|
| DEPLOY_SSH_KEY | Toàn bộ private key deploy |
| DEPLOY_HOST | hostname hoặc IP hosting |
| DEPLOY_PORT | SSH port lấy từ cPanel |
| DEPLOY_USER | SSH username của hosting |
| DEPLOY_PATH | đường dẫn thư mục public, ví dụ /home/USER/public_html |

Port 2083 trên URL cPanel là port giao diện HTTPS, không phải port SSH. SSH port cần lấy từ Clone URL trong Git Version Control; mỗi hosting có thể dùng port khác nhau.

## 10. Không chạy Docker cùng Laragon/XAMPP

Không nên chạy đồng thời Docker, Laragon và XAMPP vì có thể tranh port:

~~~text
Apache: 80 hoặc 8080
HTTPS: 443
MySQL: 3306
~~~

Nếu chuyển sang Laragon hoặc XAMPP, có thể dừng Docker bằng:

~~~powershell
docker compose down
~~~

Không dùng docker compose down -v nếu chưa muốn xóa dữ liệu MySQL Docker.

## 11. Quy trình hằng ngày

~~~text
1. Mở Laragon hoặc XAMPP
2. Start Apache và MySQL
3. Mở website local
4. Chỉnh source trong wordpress/
5. Test PHP và database
6. git add wordpress
7. git commit -m "mô tả thay đổi"
8. git push origin main
9. Kiểm tra GitHub Actions
10. Kiểm tra website production
~~~

Database local và database hosting là hai môi trường riêng. Push source PHP không tự động đồng bộ bài viết, tài khoản, database hoặc thư mục uploads.

