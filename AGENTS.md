# Quy tắc làm việc cho project

## Bắt buộc trước mỗi nhiệm vụ

1. Đọc toàn bộ file này trước khi bắt đầu.
2. Kiểm tra trạng thái project và xác định rõ phạm vi cần chỉnh sửa.
3. Chỉ sửa file hoặc thư mục được người dùng yêu cầu.
4. Không tự ý sửa, xóa hoặc di chuyển file ngoài phạm vi công việc.
5. Nếu yêu cầu chưa rõ phạm vi, hỏi lại trước khi chỉnh sửa.
6. Nếu yêu cầu liên quan đến file ngoài phạm vi cho phép, phải dừng và thông báo; không tự suy diễn hoặc thực hiện.

## Phạm vi được phép chỉnh sửa

Chỉ được phép chỉnh sửa code trong hai thư mục sau:

- `wordpress/wp-content/plugins/`
- `wordpress/wp-content/themes/`

Tuyệt đối không chỉnh sửa, xóa hoặc di chuyển file ngoài hai thư mục trên, bao gồm `wordpress/wp-config.php`, database, workflow, tài liệu và cấu hình deploy.

Nếu người dùng yêu cầu chỉnh sửa file ngoài phạm vi, phải từ chối thao tác đó và hướng dẫn người dùng tự thực hiện hoặc cập nhật quy tắc trong một phiên được phép.

Không commit file cấu hình, mật khẩu, private key hoặc secrets.

## Quy tắc Git

- Không tự ý commit hoặc push nếu người dùng chưa yêu cầu.
- Trước khi commit, kiểm tra `git diff` và `git status`.
- Commit phải mô tả đúng thay đổi.
- Chỉ stage đúng file/thư mục liên quan đến nhiệm vụ.

## Quy tắc deploy

- Không tự ý deploy production.
- Chỉ deploy sau khi người dùng xác nhận hoặc yêu cầu rõ ràng.
- Workflow GitHub Actions hiện chỉ deploy khi có thay đổi trong `wordpress/**`.
- Nếu chỉ chỉnh file ngoài hai thư mục được phép, không cần deploy.
- Không tự sửa workflow để thay đổi phạm vi deploy.

## Bảo mật

- Không in hoặc gửi nội dung private key, mật khẩu database hoặc GitHub Secret.
- Không đưa private key vào repository.
- Không đưa `wp-config.php` lên GitHub.
- Khi phát hiện secret bị lộ, phải cảnh báo người dùng và đề xuất thay key/mật khẩu.

## Kiểm tra sau khi chỉnh sửa

- Kiểm tra cú pháp hoặc test phù hợp với thay đổi.
- Tóm tắt file đã sửa.
- Nêu rõ các lệnh người dùng cần chạy tiếp theo.
