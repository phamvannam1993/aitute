<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import Footer from "../Home/Components/Footer.vue";
import {ref, computed, reactive, onMounted} from 'vue'
import { getActiveLanguage } from 'laravel-vue-i18n'

defineOptions({ layout: Layout })
</script>

<template>
    <div className="w-full text-black px-16">
        <!-- title -->
        <h1 className="capitalize text-2xl text-main-color font-bold py-8 border-b">Chính sách bảo mật</h1>
        <!-- content -->
        <div className="py-8 flex flex-col gap-2">
            <p>
                Orion AI (sau đây gọi là "công ty chúng tôi") đã thiết lập chính sách sau đây liên quan đến việc xử lý thông tin cá nhân của người dùng sử dụng dịch vụ do Công ty cung
                cấp và sẽ cố gắng bảo vệ thông tin cá nhân. Ngoài ra, chính sách bảo vệ thông tin cá nhân có thể được sửa đổi hoặc thay đổi bất cứ lúc nào mà không cần thông báo trước. Chính sách bảo
                mật mới nhất sẽ được công bố trên trang này, vì vậy thỉnh thoảng hãy kiểm tra.
            </p>
            <div>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        <h4 className="font-semibold leading-8">Tuân thủ pháp luật, hướng dẫn và quy tắc</h4>
                        <p>Công ty chúng tôi sẽ tuân thủ luật pháp và các quy định liên quan đến việc xử lý thông tin cá nhân, hướng dẫn quốc gia và các tiêu chuẩn khác. .</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Thu thập, sử dụng và cung cấp thông tin cá nhân</h4>
                        <p>
                            Khi thu thập thông tin cá nhân, công ty chúng tôi sẽ chỉ định rõ mục đích sử dụng và sẽ không xử lý thông tin cá nhân ngoài phạm vi cần thiết. Ngoài ra, chúng tôi sẽ thực
                            hiện các biện pháp để sử dụng và cung cấp thông tin cá nhân có được một cách thích hợp.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Cung cấp thông tin cá nhân ra bên ngoài</h4>
                        <p>
                            Khi thuê ngoài toàn bộ hoặc một phần việc xử lý thông tin cá nhân, chúng tôi sẽ chọn các đối tác đáp ứng đủ mức độ bảo vệ và thực hiện các biện pháp bảo vệ phù hợp thông
                            qua hợp đồng.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Các biện pháp kiểm soát bảo mật thông tin cá nhân</h4>
                        <p>
                            Công ty chúng tôi sẽ thực hiện các biện pháp để ngăn chặn và khắc phục sự rò rỉ, mất mát hoặc hư hỏng thông tin cá nhân trong phạm vi cần thiết để đạt được mục đích sử dụng
                            thông tin cá nhân một cách đúng đắn.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Câu hỏi liên quan đến thông tin cá nhân</h4>
                        <p>Công ty chúng tôi sẽ cố gắng phản hồi kịp thời và thích hợp các khiếu nại và yâu cầu tư vấn liên quan đến việc xử lý thông tin cá nhân.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Cải tiến liên tục</h4>
                        <p>Công ty chúng tôi sẽ liên tục cải thiện hệ thống quản lý bảo mật thông tin cá nhân của mình.</p>
                    </li>
                </ol>
                <div className="text-right text-sm my-8">
                    <p>Ban hành: 07 tháng 03 năm 2023</p>
                    <p>Sửa đổi lần cuối: ngày 7 tháng 3 năm 2023</p>
                    <p>Orion AI</p>
                </div>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        <h4 className="font-semibold leading-8">Xử lý thông tin người dùng</h4>
                        <p>
                            Orion AI (sau đây gọi là “công ty chúng tôi”) thiết lập chính sách bảo mật sau đây (sau đây gọi là “chính sách này”) liên quan đến việc xử lý thông tin người
                            dùng, bao gồm thông tin cá nhân của người dùng (sau đây gọi là “người dùng ") trong các dịch vụ (sau đây gọi là "dịch vụ của chúng tôi") do công ty cung cấp. Ngoài ra, Công
                            ty sẽ không chịu trách nhiệm về thông tin mà người dùng đã cung cấp cho các công ty khác sau khi đồng ý cung cấp thông tin đó cho bên thứ ba. Với sự đồng ý của công ty thứ
                            ba, thông tin có thể được hiển thị trên màn hình cho người dùng dịch vụ hoặc các sản phẩm và dịch vụ tài chính có thể được đề xuất và cung cấp dựa trên thông tin đó.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Khái niệm thông tin người dùng và thông tin cá nhân</h4>
                        <p>
                            Trong chính sách này, "thông tin người dùng" được định nghĩa là thông tin mà chúng tôi thu thập theo chính sách này, liên quan đến nhận dạng người dùng, lịch sử hành động
                            trên các dịch vụ liên lạc và thông tin khác được tạo liên quan đến người dùng hoặc thiết bị đầu cuối của người dùng. Thông tin người dùng bao gồm thông tin thuộc thông tin
                            cá nhân và thông tin không thuộc thông tin cá nhân. Ngoài ra, thông tin cá nhân đề cập ở "thông tin cá nhân" được định nghĩa trong Đạo luật bảo vệ thông tin cá nhân, là
                            thông tin về người dùng và các cá nhân sống khác bao gồm tên, ngày sinh, địa chỉ, số điện thoại, địa chỉ email, mã nhận dạng cá nhân và các mô tả khác có thể xác định cá
                            nhân đó. Ngoài ra, thông tin cá nhân bao gồm thông tin có thể dễ dàng so sánh với các thông tin khác để nhận dạng cá nhân. Nó cũng bao gồm thông tin cho phép xác định cá
                            nhân.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Thông tin người dùng được thu thập</h4>
                        <ul className="leading-6 ml-8 list-disc">
                            <li>Thông tin hồ sơ như họ tên, ngày tháng năm sinh, nghề nghiệp...</li>
                            <li>Thông tin liên hệ như địa chỉ e-mail, số điện thoại, địa chỉ...</li>
                            <li>Thông tin cá nhân cần thiết dùng để đăng nhập</li>
                            <li>Thông tin cá nhân được nhận dạng bằng công nghệ cookie</li>
                            <li>Thông tin (bao gồm cả hình ảnh) do người dùng nhập hoặc gửi thông qua biểu mẫu hoặc các phương thức khác do công ty chúng tôi chỉ định</li>
                            <li>
                                Trong trường hợp người dùng cho phép hợp tác với các dịch vụ khác khi sử dụng dịch vụ của chúng tôi, người dùng đã cho phép tiết lộ ID được người dùng sử dụng trong
                                dịch vụ bên ngoài và các thông tin khác cho đối tác thông qua cài đặt bảo mật của dịch vụ bên ngoài
                            </li>
                            <li>Nội dung và lịch sử liên lạc của e-mail và cuộc gọi giữa người dùng và công ty chúng tôi</li>
                            <li>Lịch sử người dùng trên web</li>
                            <li>Thông tin hợp đồng được xác nhận khi người dùng sử dụng dịch vụ của chúng tôi</li>
                            <li>Thông tin được Công ty thu thập dựa trên sự đồng ý cá nhân của người dùng khi người dùng sử dụng dịch vụ này</li>
                        </ul>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Mục đích sử dụng</h4>
                        <p>
                            Chúng tôi sẽ xử lý thông tin cá nhân mà bạn cung cấp cho chúng tôi một cách bí mật và sử dụng thông tin đó để hỗ trợ mối quan hệ khách hàng giữa bạn và chúng tôi hoặc để
                            cải thiện hoạt động kinh doanh hoặc dịch vụ của công ty chúng tôi và các chi nhánh của chúng tôi.
                        </p>
                        <ul className="leading-6 ml-8 list-disc">
                            <li>Mục đích cung cấp dịch vụ của công ty chúng tôi</li>
                            <li>Cung cấp thông tin về các dịch vụ của chúng tôi</li>
                            <li>Để phân tích thống kê việc sử dụng các dịch vụ của chúng tôi</li>
                            <li>Để cải thiện và phát triển dịch vụ của chúng tôi</li>
                            <li>Nâng cao chất lượng giải đáp thắc mắc và nắm bắt chính xác nội dung thắc mắc</li>
                            <li>Để sử dụng trong marketing và các chiến dịch</li>
                            <li>Công bố kết quả xử lý thống kê các thông tin thu nhận được</li>
                            <li>Để phục vụ hoặc hiển thị quảng cáo của chúng tôi hoặc bên thứ ba</li>
                            <li>Hỗ trợ kinh doanh và liên hệ như đề cập phía trên</li>
                            <li>Đối ứng với thủ tục</li>
                            <li>Nâng cao chất lượng giải đáp thắc mắc và nắm bắt chính xác nội dung thắc mắc</li>
                            <li>Hỗ trợ kinh doanh và liên hệ như đề cập phía trên</li>
                            <li>Đối ứng với thủ tục</li>
                            <li>Để ngăn chặn và điều tra việc sử dụng trái phép và liên hệ với bạn khi phát hiện ra việc sử dụng trái phép</li>
                        </ul>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Việc cung cấp thông tin cá nhân cho bên thứ ba</h4>
                        <p>
                            Thông tin cá nhân do khách hàng cung cấp có thể được tiết lộ hoặc cung cấp cho bên thứ ba khi được sự đồng ý của khách hàng hoặc trong các trường hợp sau. Trong các trường
                            hợp khác, chúng tôi sẽ không tiết lộ hoặc cung cấp thông tin cá nhân cho bên thứ ba.
                        </p>
                        <ul className="leading-6 ml-8 list-disc">
                            <li>
                                Khi ủy thác toàn bộ hoặc một phần việc xử lý thông tin cá nhân cho các công ty khác trong phạm vi cần thiết để đạt được mục đích mô tả trong "Mục đích sử dụng thông tin
                                cá nhân" ở trên
                            </li>
                            <li>Khi cung cấp thông tin cần thiết cho các tổ chức tài chính như ngân hàng liên quan đến việc thanh toán khi mua gói dịch vụ...</li>
                            <li>Nếu khách hàng gây thiệt hại cho công ty hoặc nếu công ty xác định rằng có rủi ro khi làm như vậy</li>
                            <li>Khi chúng tôi nhận được yêu cầu từ các tổ chức công cộng như tòa án, hiệp hội luật sư, cảnh sát, trung tâm người tiêu dùng...</li>
                            <li>Ngoài ra, khi có quy định của pháp luật</li>
                        </ul>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Ủy thác xử lý thông tin cá nhân</h4>
                        <p>
                            Chúng tôi có thể thuê ngoài toàn bộ hoặc một phần việc xử lý thông tin cá nhân cho các bên thuê ngoài (bao gồm cả các bên bên ngoài Nhật Bản) trong phạm vi cần thiết để đạt
                            được mục đích sử dụng. Chúng tôi sẽ chọn nhà thầu phù hợp sau khi đánh giá từ góc độ bảo vệ thông tin cá nhân và bảo mật thông tin, đồng thời ký kết hợp đồng phù hợp với
                            nhà thầu bao gồm chi tiết các biện pháp kiểm soát bảo mật. Để đảm bảo việc quản lý an toàn thông tin cá nhân được ủy thác xử lý, chúng tôi sẽ thực hiện sự giám sát cần
                            thiết và phù hợp đối với bên được ủy thác.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Về việc sử dụng cookie</h4>
                        <p>
                            Trong trang web, Công ty thu thập các tệp nhật ký nhằm mục đích ngăn chặn gian lận, cải thiện khả năng sử dụng của trang web, thu thập thông tin thống kê như trạng thái
                            truy cập, đo lường hiệu quả quảng cáo, gửi cookie của Công ty hoặc bên thứ ba. Bằng cách cài đặt beacons, chúng tôi sẽ có được thông tin như thông tin cookie của người
                            dùng, địa chỉ IP, số lần truy cập và môi trường sử dụng. Việc sử dụng cookie có thể bị ngăn chặn bởi cài đặt của người dùng. Nếu bạn chặn cookie, bạn có thể không sử dụng
                            được các dịch vụ của chúng tôi một cách bình thường.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Thông tin được xử lý ẩn danh</h4>
                        <p>
                            Về thông tin cá nhân của người đồng ý với chính sách này, chúng tôi sẽ tuân thủ các quy tắc của Ủy ban bảo vệ thông tin cá nhân theo Điều 36, Đoạn 1 của Đạo luật. Sau khi
                            thực hiện các biện pháp bảo vệ thích hợp để ngăn chặn việc nhận dạng một cá nhân cụ thể theo các tiêu chuẩn đã thiết lập và khôi phục thông tin cá nhân được sử dụng để tạo,
                            chúng tôi sẽ tiếp tục tạo thông tin được xử lý ẩn danh và cung cấp thông tin đó cho bên thứ ba trong phạm vi được pháp luật cho phép.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Những thay đổi đối với chính sách bảo mật</h4>
                        <p>Nếu chúng tôi có những thay đổi đáng kể về chính sách bảo mật của mình, chúng tôi sẽ có thông báo trên trang web</p>
                    </li>
                </ol>
                <div className="text-right text-sm my-8">
                    <p>Các thắc mắc về thông tin cá nhân</p>
                    <p>Bộ phận tư vấn thông tin cá nhân của Orion AI</p>
                    <p>Liên hệ: gotechjsc@gmail.com</p>
                </div>
            </div>
        </div>
    </div>
    <Footer class="mt-auto" />
    
</template>
