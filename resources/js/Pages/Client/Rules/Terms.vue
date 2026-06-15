<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import {ref, computed, reactive, onMounted} from 'vue'
import { getActiveLanguage } from 'laravel-vue-i18n'
import Footer from "../Home/Components/Footer.vue";

const lang = ref(getActiveLanguage());

onMounted(() => {
    lang.value = getActiveLanguage()
})
defineOptions({ layout: Layout })
</script>

<template>
    <div className="w-full px-16 text-black">
        <!-- title -->
        <h1 className="capitalize text-2xl text-main-color font-bold py-8 border-b">Điều khoản dịch vụ</h1>
        <!-- content -->
        <div className="py-8 flex flex-col gap-2">
            <p>
                Orion AI là dịch vụ chỉ được cung cấp cho những người đồng ý với các điều khoản sử dụng sau đây. Các điều kiện để cung cấp dịch vụ này và các quyền và nghĩa vụ giữa "Orion AI" và người
                dùng được quy định. Vui lòng đọc toàn bộ các Điều khoản này trước khi sử dụng Dịch vụ.
            </p>
            <!-- article 1 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 1 (Mục đích)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Điều khoản và điều kiện sử dụng "Orion AI" (sau đây gọi là "điều khoản sử dụng ") sẽ áp dụng cho những khách hàng sử dụng dịch vụ của Orion AI được cung cấp trên trang web này cũng
                        như các dịch vụ khác (Sau đây gọi chung là "Dịch vụ") do công ty cung cấp.
                    </li>
                    <li>
                        Trong trường hợp công ty công bố các điều khoản riêng lẻ hoặc các điều khoản bổ sung liên quan đến dịch vụ trên trang web của công ty, các Điều khoản này sẽ tạo thành một phần
                        của thỏa thuận này. Nếu các điều khoản riêng lẻ hoặc các điều khoản bổ sung xung đột với các điều khoản này, thì các điều khoản riêng lẻ hoặc các điều khoản bổ sung sẽ được ưu
                        tiên áp dụng.
                    </li>
                </ol>
            </div>
            <!-- article 2 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 2 (Định nghĩa)</h3>
                <p className="text-sm leading-6">Trong các Điều khoản này, định nghĩa của các thuật ngữ được liệt kê trong các mục sau đây sẽ được quy định cụ thể trong từng mục.</p>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        <h4 className="font-semibold leading-8">Tài khoản</h4>
                        <p>Tư cách hoặc quyền lợi của người dùng để sử dụng dịch vụ này.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Nội dung</h4>
                        <p>
                            Một thuật ngữ chung cho thông tin (Bao gồm nhưng không giới hạn ở văn bản, hình ảnh, video, giọng nói, âm nhạc và các âm thanh, hình ảnh, phần mềm, chương trình, mã và dữ
                            liệu khác.) mà người dùng có thể truy cập thông qua dịch vụ này.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Website của chúng tôi</h4>
                        <p>Đề cập đến hợp đồng liên quan đến việc sử dụng dịch vụ được ký kết giữa công ty và Người sử dụng dịch vụ theo các thủ tục được quy định trong các Điều khoản này.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Điều khoản sử dụng</h4>
                        <p>Đề cập đến hợp đồng liên quan đến việc sử dụng dịch vụ được ký kết giữa công ty và Người sử dụng dịch vụ theo các thủ tục được quy định trong các Điều khoản này.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Người đăng kí</h4>
                        <p>
                            Đề cập đến nhà điều hành doanh nghiệp (Các công ty, tổ chức khác và cá nhân trở thành các bên của hợp đồng với tư cách là một doanh nghiệp hoặc mục đích kinh doanh. Điều
                            tương tự sẽ được áp dụng sau đây.) đăng ký sử dụng dịch vụ.
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Người dùng</h4>
                        <p>Đề cập đến một nhà điều hành doanh nghiệp đã ký kết thỏa thuận sử dụng theo các thủ tục được quy định trong các điều khoản này và đã đăng ký sử dụng dịch vụ.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Dịch vụ liên kết</h4>
                        <p>
                            Bao gồm nhưng không giới hạn ở các trang web do Công ty chỉ định tùy ý trong số các trang web do bên thứ ba vận hành và quản lý (1) Các trang web liên quan đến thông tin kế
                            toán của người dùng hoặc những người có liên quan đến người dùng; (2) Các trang web liên quan đến thông tin mua bán,③ tài khoản ngân hàng,④ thông tin thẻ, các trang web
                            khác...
                        </p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">ID dịch vụ liên kết</h4>
                        <p>Mã được sử dụng kết hợp với ID dịch vụ được liên kết để phân biệt người dùng với những người khác.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Mật khẩu dịch vụ liên kết</h4>
                        <p>Mã được sử dụng kết hợp với Mật khẩu dịch vụ được liên kết để phân biệt Người dùng với những người khác.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Công ty</h4>
                        <p>Đề cập đến một nhóm sử dụng dịch vụ cùng với một hoặc nhiều tài khoản.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Quản trị viên</h4>
                        <p>Người dùng, với tư cách là quản trị viên của một công ty, có quyền thêm hoặc xóa thành viên trong cùng một công ty.</p>
                    </li>
                    <li>
                        <h4 className="font-semibold leading-8">Thành viên</h4>
                        <p>Người dùng được thêm vào Công ty bởi quản trị viên</p>
                    </li>
                </ol>
            </div>
            <!-- article 3 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 3 (Đồng ý với các Điều khoản này)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>Người dùng sẽ sử dụng dịch vụ này theo điều khoản này và sẽ không sử dụng dịch vụ này trừ khi họ đồng ý với điều khoản.</li>
                    <li>
                        Vào thời điểm người đăng ký đồng ý với các điều khoản này và hoàn tất đăng ký sử dụng dịch vụ, thỏa thuận sử dụng phù hợp với các quy định của các điều khoản này sẽ được thiết
                        lập giữa người đăng ký và công ty.
                    </li>
                </ol>
            </div>
            <!-- article 4 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 4 (Đăng ký sử dụng)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Các ứng viên muốn sử dụng dịch vụ này đồng ý tuân thủ các điều khoản này theo các trình tự thủ tục do Công ty chúng tôi xác định. Bằng cách cung cấp thông tin và tài liệu (sau
                        đây gọi là "thông tin người dùng") cho công ty chúng tôi theo phương pháp do công ty chúng tôi chỉ định, bạn có thể đăng ký sử dụng dịch vụ.
                    </li>
                    <li>
                        Nếu công ty xác định rằng Người đăng ký thuộc bất kỳ mục nào dưới đây hoặc theo quyết định khác theo quyết định của công ty, công ty có thể không phê duyệt đơn đăng ký sử dụng
                        dịch vụ của người đăng ký hoặc có thể hủy đăng ký sử dụng đã được phê duyệt. Ngay cả khi người đăng ký bị từ chối hoặc đăng ký sử dụng bị hủy bỏ, người đăng ký không thể yêu
                        cầu tiết lộ lý do hoặc gửi đơn phản đối đến công ty.<br />(1) Nếu có bất kỳ thông tin giả, nhầm lẫn hoặc sai sót nào trong toàn bộ hoặc một phần thông tin hoặc tài liệu mà
                        Người đăng ký cung cấp cho công ty khi đăng ký sử dụng dịch vụ; <br />(2) Nếu Người đăng ký đã nhận hoặc đang nhận đình chỉ dịch vụ đối với dịch vụ này hoặc dịch vụ khác do
                        công ty cung cấp, hoặc nếu hợp đồng sử dụng dịch vụ đã bị hủy bỏ. <br />(3) Trước đây, nếu bạn đã bỏ qua việc thực hiện các nghĩa vụ theo hợp đồng sử dụng này và các nghĩa vụ
                        theo hợp đồng khác đã ký kết với chúng tôi hoặc nếu công ty xác định rằng có rủi ro khi bỏ qua việc thực hiện các nghĩa vụ khác theo thỏa thuận sử dụng này <br />(4) Nếu việc
                        cung cấp Dịch vụ cho Người đăng ký có khả năng cản trở hoạt động của công ty hoặc gây ra sự cố hệ thống <br />(5) Ngoài ra, khi Công ty xác định rằng yêu cầu đăng kí dùng dịch
                        vụ không phù hợp
                    </li>
                    <li>
                        Nếu công ty chúng tôi chấp thuận đơn đăng ký sử dụng dịch vụ, công ty chúng tôi sẽ thông báo cho người dùng về việc đó. Tại thời điểm thông báo được gửi đi, hợp đồng sẽ được
                        thiết lập giữa người nộp đơn và công ty.
                    </li>
                </ol>
            </div>
            <!-- article 5 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 5 (Cung cấp thông tin để sử dụng dịch vụ này)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>Để sử dụng dịch vụ này, người dùng cung cấp tất cả hoặc một phần thông tin như công ty chỉ định.</li>
                    <li>
                        Trong thông tin được cung cấp trong phần trước, bao gồm thông tin liên quan đến các dịch vụ liên kết, người dùng phải cho phép công ty truy cập dịch vụ được liên kết có liên
                        quan bằng cách sử dụng ID dịch vụ liên kết và mật khẩu dịch vụ liên kết hoặc thông qua một hệ thống như API của bên thứ ba cung cấp dịch vụ liên kết. Lưu ý rằng công ty sẽ
                        không chịu trách nhiệm cho bất kỳ thiệt hại nào mà người dùng hoặc bên thứ ba phải gánh chịu do sử dụng ID dịch vụ liên kết, mật khẩu dịch vụ liên kết hoặc kết nối API.
                    </li>
                    <li>
                        Khi cung cấp thông tin cá nhân cho công ty, người dùng phải được sự đồng ý trước bằng văn bản của người được đề cập, đồng thời lưu trữ và quản lý tài liệu này để có thể gửi cho
                        công ty bất cứ lúc nào nếu có yêu cầu từ công ty.
                    </li>
                    <li>Chúng tôi mặc định các thông tin cá nhân được cung cấp cho chúng tôi đã nhận được sự đồng ý của cá nhân đó.</li>
                    <li>
                        Các thành viên và quản trị viên của công ty đồng ý cho phép các thành viên khác và quản trị viên khác của công ty sử dụng thông tin của họ trong quá trình sử dụng dịch vụ này.
                    </li>
                </ol>
            </div>
            <!-- article 6 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 6 (Quản lý tài khoản)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Người dùng không được cho bên thứ ba sử dụng tài khoản, không được chuyển nhượng, mua/bán tài khoản.Về việc sử dụng tài khoản, nếu công ty xác nhận rằng ID đăng nhập hoặc mật
                        khẩu liên quan đến việc sử dụng tài khoản và các thông tin hoặc chuỗi ký tự khác được công ty yêu cầu xuất trình hoặc nhập để sử dụng tài khoản trùng khớp, thì người đăng ký
                        tài khoản sẽ được coi là đã đăng kí sử dụng dịch vụ.
                    </li>
                    <li>Người dùng chịu mọi trách nhiệm về việc quản lý và sử dụng tài khoản được cấp theo các điều khoản này và không gây thiệt hại cho Công ty.</li>
                    <li>Người dùng sẽ chịu mọi trách nhiệm pháp lý liên quan đến việc sử dụng dịch vụ bằng tài khoản của chính mình (bao gồm cả việc sử dụng bởi bên thứ ba).</li>
                    <li>Người dùng có trách nhiệm & tự trả chi phí duy trì môi trường đầu cuối bằng cách thực hiện bảo trì phần cứng và phần mềm.</li>
                    <li>Nếu thông tin của người dùng bị đánh cắp hoặc bị phát hiện sử dụng bởi bên thứ ba, người dùng phải thông báo ngay cho công ty về việc đó và làm theo hướng dẫn của công ty.</li>
                    <li>Nếu có bất kỳ thay đổi nào trong các mục đã đăng ký, người dùng phải thông báo ngay cho công ty về sự thay đổi đó bằng phương pháp do công ty chỉ định.</li>
                    <li>Quản trị viên chịu trách nhiệm về bất kỳ hành động nào của thành viên khi thêm thành viên vào.</li>
                </ol>
            </div>
            <!-- article 7 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 7 (Những điều cấm)</h3>
                <p className="text-sm leading-8 mb-2">Khi sử dụng dịch vụ này, người dùng không được thực hiện bất kỳ hành vi nào sau đây hoặc hành vi có thể thuộc bất kỳ hành vi nào sau đây:</p>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>Các hành vi trái với trật tự và đạo đức công cộng.</li>
                    <li>Hành vi vi phạm pháp luật và các quy định.</li>
                    <li>Hành vi phạm tội và hành vi dẫn đến hành vi phạm tội.</li>
                    <li>Hành vi cung cấp thông tin sai sự thật cho công ty.</li>
                    <li>Hành vi làm giả/sai lệch thông tin có thể truy cập bởi dịch vụ này</li>
                    <li>Các hành vi có thể gây trở ngại cho hoạt động của dịch vụ này.</li>
                    <li>Hành vi sử dụng/công khai thông tin hoặc nội dung khác do công ty cung cấp vượt quá phạm vi sử dụng được công ty cho phép.</li>
                    <li>Hành vi xâm phạm quyền sở hữu trí tuệ, quyền sử dụng hình ảnh, quyền riêng tư, danh dự hoặc các quyền hoặc lợi ích khác của công ty hoặc bên thứ ba.</li>
                    <li>Hành vi khai thác hoặc phát tán các lỗi ngoài ý muốn, các công cụ bên ngoài có tác dụng ngoài ý muốn.</li>
                    <li>Hành vi gửi thông tin chứa virus máy tính hoặc các chương trình máy tính có hại khác.</li>
                    <li>Hành vi phân giải, dịch ngược, đảo ngược hoặc phân tích source code của dịch vụ.</li>
                    <li>Hành vi sao chép, chuyển nhượng, cho mượn hoặc sửa đổi dịch vụ.</li>
                    <li>Hành vi sử dụng trái phép tài khoản, mật khẩu của người dùng khác.</li>
                    <li>Các hành vi gây bất lợi cho bên thứ ba.</li>
                    <li>Các hành vi khác mà công ty chúng tôi đánh giá là không phù hợp.</li>
                </ol>
            </div>
            <!-- article 8 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 8 (Các biện pháp xử lý khi vi phạm điều khoản)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Nếu công ty xác định rằng người dùng thuộc hoặc có khả năng thuộc bất kỳ mục nào sau đây, công ty có thể quyết định áp dụng các biện pháp sau mà không cần thông báo: xóa toàn
                        bộ hoặc một phần thông tin người dùng, tạm dừng hoặc hạn chế sử dụng dịch vụ, xóa tài khoản hoặc hủy bỏ thỏa thuận sử dụng (sau đây gọi là “tạm ngưng tài khoản)
                        <ul className="list-disc ml-8">
                            <li>Nếu bạn vi phạm bất kỳ phần nào của điều khoản này</li>
                            <li>Nếu tất cả hoặc một phần thông tin cung cấp cho công ty chúng tôi là không chính xác.</li>
                            <li>Nếu không nhận được phản hồi sau hơn 30 ngày kể từ ngày liên hệ yêu cầu phản hồi</li>
                            <li>Khi công ty xác cân nhắc cần thiết cho việc vận hành và bảo trì dịch vụ</li>
                            <li>Nếu công ty xác định rằng có những lý do khác tương tự như các lý do trên.</li>
                        </ul>
                    </li>
                    <li>
                        Ngay cả sau khi tài khoản bị tạm ngưng, người dùng không được miễn trừ mọi nghĩa vụ và trách nhiệm pháp lý (bao gồm nhưng không giới hạn ở trách nhiệm bồi thường thiệt hại) đối
                        với công ty theo điều khoản sử dụng này.
                    </li>
                    <li>
                        Công ty sẽ không chịu trách nhiệm cho bất kỳ bất lợi hoặc thiệt hại nào gây ra cho người dùng do các hành động của công ty theo điều khoản sử dụng này và ngay cả sau khi tài
                        khoản của người dùng bị xóa, công ty có thể giữ lại và sử dụng thông tin mà công ty có được liên quan đến người dùng.
                    </li>
                </ol>
            </div>
            <!-- article 9 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 9 (Người dùng huỷ đăng ký)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Người dùng có thể hủy thỏa thuận sử dụng này bằng cách thông báo cho công ty theo cách thức do công ty quy định. Tuy nhiên, nếu người dùng nợ công ty bất kỳ nghĩa vụ nào, việc
                        hủy bỏ sẽ tùy thuộc vào việc thanh toán các nghĩa vụ đó.
                    </li>
                    <li>Trong trường hợp trên, công ty có thể xóa tài khoản của người dùng.</li>
                    <li>
                        Công ty sẽ không chịu trách nhiệm về bất kỳ bất lợi hoặc thiệt hại nào mà người dùng phải gánh chịu do việc chấm dứt sử dụng theo điều khoản này, đồng thời sẽ giữ lại và sử
                        dụng thông tin mà công ty có được về người dùng ngay cả sau khi tài khoản của người dùng bị xóa.
                    </li>
                    <li>
                        Người dùng là thành viên của một công ty lưu ý rằng ngay cả sau khi bị chủ sở hữu xóa khỏi công ty hoặc tự nguyện rút khỏi công ty, thông tin của người dùng trong cùng một công
                        ty sẽ được các thành viên và chủ sở hữu của cùng một công ty lưu giữ và sử dụng
                    </li>
                </ol>
            </div>
            <!-- article 10 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 10 (Chấm dứt sử dụng)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Thỏa thuận sử dụng dịch vụ sẽ bị chấm dứt theo hai điều trước hoặc vì bất kỳ lý do nào khác. Nếu Người dùng mất quyền sử dụng dịch vụ, người dùng phải đồng ý trước rằng họ sẽ
                        không thể sử dụng dịch vụ này và sẽ không thể sử dụng tài khoản, nội dung hoặc thông tin khác được thu thập trong dịch vụ này.
                    </li>
                    <li>
                        Ngay cả sau khi chấm dứt sử dụng dịch vụ, người dùng sẽ không được miễn trừ mọi trách nhiệm và nghĩa vụ (bao gồm nhưng không giới hạn ở việc bồi thường thiệt hại) đối với công
                        ty và bên thứ ba theo điều khoản sử dụng dịch vụ này.
                    </li>
                    <li>
                        Công ty sẽ lưu giữ và sử dụng thông tin (bao gồm, nhưng không giới hạn ở thông tin người dùng và thông tin liên quan đến giao dịch) mà Công ty có được về Người dùng ngay cả sau
                        khi Người dùng đã sử dụng xong Dịch vụ.
                    </li>
                    <li>Người dùng là thành viên của công ty phải đồng ý rằng nếu quản trị viên xóa người dùng khỏi tư cách thành viên của công ty, người dùng sẽ tự động bị ngưng dịch vụ.</li>
                    <li>
                        Trong trường hợp trên, người dùng đồng ý trước rằng họ sẽ không thể sử dụng dịch vụ và sẽ không thể sử dụng tài khoản, nội dung hoặc bất kỳ thông tin nào khác liên quan đến
                        dịch vụ này.
                    </li>
                </ol>
            </div>
            <!-- article 11 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 10 (Chấm dứt sử dụng)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Mọi thông tin liên lạc từ công ty tới người dùng về dịch vụ (bao gồm nhưng không giới hạn ở các thông báo về các thay đổi hoặc bổ sung đối với các điều khoản này) sẽ được đăng
                        trên các vị trí thích hợp trong trang web của Công ty. Việc này sẽ được thực hiện bằng cách thông báo, gửi e-mail hoặc bất kỳ phương pháp nào khác mà Công ty cho là phù hợp.
                    </li>
                    <li>
                        Trong trường hợp công ty thông báo bằng cách gửi e-mail, email từ công ty sẽ được coi là đã đến đúng thời điểm như thông thường. Công ty sẽ gửi mail đến địa chỉ e-mail mà người
                        dùng đăng kí
                    </li>
                    <li>
                        Các câu hỏi liên quan đến dịch vụ và các liên lạc hoặc thông báo khác từ người dùng đến công ty sẽ được thực hiện bằng cách gửi e-mail đến địa chỉ yêu cầu liên quan đến dịch vụ
                        được nêu trong biểu mẫu hoặc một vị trí thích hợp trong trang web của Công ty hoặc bằng cách khác các phương pháp do Công ty chỉ định.
                    </li>
                    <li>Người dùng là thành viên của công ty phải đồng ý rằng nếu quản trị viên xóa người dùng khỏi tư cách thành viên của công ty, người dùng sẽ tự động bị ngưng dịch vụ.</li>
                    <li>
                        Công ty có thể liên hệ với người dùng về quảng cáo, khuyến mãi liên quan đến dịch vụ này dựa trên địa chỉ e-mail và các thông tin khác do người dùng đăng ký và người dùng phải
                        đồng ý trước với điều này.
                    </li>
                </ol>
            </div>
            <!-- article 12 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 10 (Chấm dứt sử dụng)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>Người dùng sẽ bảo mật mọi thông tin không công khai (sau đây gọi là "Thông tin bảo mật") do công ty tiết lộ liên quan đến dịch vụ sau khi chỉ định thông tin đó là bí mật.</li>
                    <li>Người sử dụng phải lưu trữ và quản lý thông tin mật một cách chặt chẽ, không được tiết lộ, rò rỉ cho bên thứ ba khi chưa được sự đồng ý trước bằng văn bản của Công ty.</li>
                </ol>
            </div>
            <!-- article 13 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 13 (Xử lý thông tin cá nhân)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Chúng tôi sẽ xử lý đúng cách thông tin cá nhân có trong thông tin người dùng, thông tin thu được theo Điều 5 của điều khoản này và các thông tin khác liên quan đến người dùng
                        dựa trên "Chính sách bảo vệ thông tin cá nhân" được đăng riêng trên trang web của chúng tôi.
                    </li>
                    <li>
                        Theo như "Chính sách bảo vệ thông tin cá nhân", Công ty có thể thuê ngoài việc xử lý thông tin cá nhân ở phần trước đó trong điều khoản này cho một nhà thầu phụ trong phạm vi
                        cần thiết cho việc cung cấp dịch vụ này và các hoạt động liên quan.
                    </li>
                </ol>
            </div>
            <!-- article 14 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 14 (Thay đổi dịch vụ)</h3>
                <p className="text-sm leading-6 mb-2">
                    Công ty có thể thêm hoặc thay đổi một phần nội dung của Dịch vụ theo quyết định của công ty. Công ty không đảm bảo rằng tất cả các chức năng và hoạt động của Dịch vụ trước khi thay
                    đổi sẽ được duy trì do các bổ sung hoặc thay đổi đối với Dịch vụ theo điều khoản này.
                </p>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>Công ty sẽ không chịu trách nhiệm cho bất kỳ thiệt hại hoặc bất lợi nào mà Người dùng phải gánh chịu do các bổ sung hoặc thay đổi đối với dịch vụ đề cập trên.</li>
                    <li>
                        Công ty sẽ không chịu trách nhiệm cho bất kỳ thiệt hại nào do người dùng hoặc bên thứ ba gây ra liên quan đến dịch vụ, ngoại trừ các trường hợp do sơ suất cố ý hoặc nghiêm
                        trọng của công ty. Ngoài ra, công ty sẽ chịu trách nhiệm về những thiệt hại giới hạn ở những thiệt hại trực tiếp và thông thường (không bao gồm thua lỗ, thiệt hại gián tiếp,
                        thiệt hại đặc biệt, thiệt hại mang tính trừng phạt) đã thực sự xảy ra, và đối với các dịch vụ phải chi trả, số tiền thanh toán là giới hạn tương đương 3 tháng sử dụng dịch vụ
                        liên tục. Ngay cả khi miễn trừ trên không hợp lệ theo luật tiêu dùng hoặc các luật hiện hành khác, công ty sẽ chấp nhận miễn trừ tối đa được luật hiện hành quy định.
                    </li>
                </ol>
            </div>
            <!-- article 15 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 15 (Xử lý thông tin người dùng)</h3>
                <p className="text-sm leading-6 mb-2">Công ty có được thông tin mà người dùng cung cấp cho công ty thông qua dịch vụ cho các mục đích sau.</p>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>Hướng dẫn về dịch vụ này và các dịch vụ khác do công ty cung cấp</li>
                    <li>Thông báo/phản hồi về việc đình chỉ, hủy bỏ hoặc hủy bỏ hợp đồng của dịch vụ</li>
                    <li>Thông báo về các thay đổi đối với các điều khoản và chính sách của chúng tôi liên quan đến dịch vụ này và phản hồi các tranh chấp, kiện cáo</li>
                    <li>Để trả lời các câu hỏi liên quan đến dịch vụ này</li>
                    <li>Để cung cấp, duy trì, cải thiện và phát triển dịch vụ đi kèm với các mục trước đó</li>
                    <li>
                        Khi cung cấp thông tin nhà thầu cho bên thứ ba trong phạm vi hợp lý để liên kết dịch vụ này với dịch vụ do công ty chúng tôi và các chi nhánh của chúng tôi cung cấp hoặc với
                        dịch vụ bên thứ ba và cải thiện chức năng của nó
                    </li>
                    <li>
                        Công ty chúng tôi phân tích thông tin người dùng, tạo ra các thông tin đã được xử lý để không thể xác định được các công ty hoặc cá nhân riêng lẻ và sử dụng thông tin đó trong
                        nội bộ hoặc tiết lộ cho bên thứ ba nhằm mục đích cải thiện, phát triển, nghiên cứu, phân tích thị trường và marketing cho dịch vụ.
                    </li>
                </ol>
            </div>
            <!-- article 16 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 16 (Xử lý thông tin người dùng)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>Công ty có thể cân nhắc và đơn phương quyết định ngừng cung cấp dịch vụ.</li>
                    <li>
                        Công ty có thể tạm thời đình chỉ tất cả hoặc một phần dịch vụ trong trường hợp xảy ra bất kỳ sự kiện nào sau đây:
                        <ul className="list-disc ml-8">
                            <li>Khi thực hiện bảo trì hệ thống định kỳ hoặc khẩn cấp</li>
                            <li>Khi lượng truy cập tập trung vào hệ thống quá tải do truy cập quá mức hoặc các yếu tố không mong muốn khác</li>
                            <li>Khi cần phải đảm bảo giữ an toàn cho người dùng.</li>
                            <li>Khi dịch vụ của nhà cung cấp dịch vụ viễn thông không được cung cấp</li>
                            <li>Khi toàn bộ hoặc một phần dịch vụ của bên thứ ba không được cung cấp được</li>
                            <li>Khi không thể cung cấp được dịch vụ do phát sinh sự cố như mất điện, hoả hoạn,...</li>
                            <li>Khi không thể cung cấp dịch vụ do thiên tai như lũ lụt, động đất, sóng thần, núi lửa phun trào,....</li>
                            <li>Khi không thể cung cấp dịch vụ do chiến tranh, loạn lạc, bạo loạn, xáo trộn, tranh chấp lao động, ....</li>
                            <li>Khi không thể vận hành dịch vụ này do bị ảnh hưởng hoặc bị giới hạn bởi các quy định pháp luật hoặc các biện pháp được áp dụng theo quy định pháp luật</li>
                            <li>Khi công ty chúng tôi cân nhắc cần thiết dựa trên các điều đã nêu trên</li>
                        </ul>
                    </li>
                    <li>
                        Khi công ty chúng tôi quyết định ngừng hoặc gián đoạn hoạt động của dịch vụ như đã đề cập ở 2 điều ghi trên, chúng tôi sẽ thông báo cho người dùng trước thông qua các phương
                        tiện như hiển thị trên tài khoản người dùng hoặc trang web của của công ty theo cách chúng tôi xem là phù hợp. Tuy nhiên, trong trường hợp khẩn cấp và không thể tránh được,
                        điều này sẽ không áp dụng.
                    </li>
                    <li>Công ty chúng tôi sẽ không chịu trách nhiệm về bất kỳ thiệt hại nào do biện pháp mà công ty đã thực hiện dựa trên điều khoản này mà gây ra cho người dùng.</li>
                    <li>
                        Ngay cả sau khi tài khoản bị tạm ngưng, người dùng không được miễn trừ mọi nghĩa vụ và trách nhiệm pháp lý (bao gồm nhưng không giới hạn ở trách nhiệm bồi thường thiệt hại) đối
                        với công ty theo điều khoản sử dụng này.
                    </li>
                    <li>
                        Công ty sẽ lưu giữ và sử dụng thông tin (bao gồm, nhưng không giới hạn ở thông tin người dùng và thông tin liên quan đến giao dịch) mà Công ty có được về Người dùng ngay cả sau
                        khi Người dùng đã sử dụng xong Dịch vụ.
                    </li>
                </ol>
            </div>
            <!-- article 17 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 17 (Thay đổi đối với các Điều khoản này)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Công ty có thể thay đổi các điều khoản này bất cứ lúc nào mà không cần có sự đồng ý trước của Người dùng. Trừ khi công ty có các quy định khác, các điều khoản sửa đổi sẽ có
                        hiệu lực kể từ thời điểm được đăng trên trang web của công ty.
                    </li>
                    <li>
                        Các điều khoản và điều kiện sử dụng của dịch vụ này sẽ tuân theo các điều khoản sử dụng đã thay đổi và nếu người dùng không đồng ý với các điều khoản sử dụng đã thay đổi, người
                        dùng sẽ ngay lập tức chấm dứt việc sử dụng dịch vụ này.
                    </li>
                    <li>
                        Nếu người dùng tiếp tục sử dụng dịch vụ sau khi điều khoản đã được thay đổi, người dùng sẽ được coi là đã đồng ý với điều khoản đã thay đổi. Người dùng có trách nhiệm xác nhận
                        nội dung mới nhất của thỏa thuận này và có thể tiếp dục sử dụng dịch vụ.
                    </li>
                </ol>
            </div>
            <!-- article 18 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 18 (Quyền sở hữu trí tuệ) )</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Công ty có thể thay đổi các điều khoản này bất cứ lúc nào mà không cần có sự đồng ý trước của Người dùng. Trừ khi công ty có các quy định khác, các điều khoản sửa đổi sẽ có
                        hiệu lực kể từ thời điểm được đăng trên trang web của công ty.
                    </li>
                    <li>
                        Công ty cấp cho người dùng giấy phép không độc quyền để sử dụng trang web của công ty, dịch vụ và nội dung được cung cấp trong phạm vi cần thiết cho việc sử dụng dịch vụ. Tuy
                        nhiên, giấy phép đó không bao gồm quyền cấp phép phụ cho bên thứ ba và người dùng sẽ không được hưởng mọi quyền sở hữu trí tuệ hoặc quyền sở hữu tương tự liên quan đến trang
                        web của chúng tôi, dịch vụ này hay nội dung được cung cấp. Nó không ngụ ý chuyển nhượng hoặc cấp bất kỳ quyền nào giống như quyền sở hữu hoặc quyền sử dụng tự do hoặc các quyền
                        khác.
                    </li>
                    <li>
                        Trên trang web của công ty chúng tôi có thể hiển thị các thương hiệu, logo và ký hiệu dịch vụ của chúng tôi (gọi chung là "Nhãn hiệu thương mại"). Tuy nhiên, chúng tôi không
                        chuyển nhượng hoặc cấp phép sử dụng " Nhãn hiệu thương mại " cho người dùng hoặc bất kỳ bên thứ ba nào khác.
                    </li>
                </ol>
            </div>
            <!-- article 19 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 19 (Miễn trừ trách nhiệm)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Công ty chúng tôi không đảm bảo rằng dịch vụ này sẽ tương thích với tất cả các thiết bị và người dùng phải hiểu rằng sẽ có khả năng gặp sự cố trong hoạt động của dịch vụ khi
                        cập nhật phiên bản hệ điều hành hoặc trình duyệt web trên thiết bị được sử dụng để truy cập dịch vụ. Chúng tôi không đảm bảo rằng các sự cố này sẽ được giải quyết thông qua
                        việc sửa chữa chương trình do chúng tôi thực hiện nếu có sự cố xảy ra.
                    </li>
                    <li>
                        Công ty chúng tôi không đảm bảo bất kỳ tính toàn vẹn, tính liên tục, tính phù hợp cho mục đích cụ thể, tính phù hợp với yêu cầu của đạo luật bảo quản sách điện tử hoặc bất kỳ
                        cam kết nào khác về dịch vụ của chúng tôi. Chúng tôi không chịu trách nhiệm cho bất kỳ nguyên nhân nào mà người sử dụng không thể sử dụng dịch vụ của chúng tôi, bao gồm các sự
                        cố về đường truyền hoặc bất kỳ lý do nào khác.
                    </li>
                    <li>
                        Dù có bất kỳ lý do gì khiến cho công ty chúng tôi phải chịu trách nhiệm, chúng tôi cũng sẽ không bồi thường cho các thiệt hại đi kèm, thiệt hại gián tiếp, thiệt hại đặc biệt
                        hoặc các mất mát.
                    </li>
                    <li>
                        Ngoài dịch vụ hỗ trợ tạo CV công ty chúng tôi còn đại diện để xử lý các thủ tục liên quan tới việc gửi CV. Tuy nhiên việc gửi CV công ty chúng tôi sẽ uỷ thác cho bên thứ ba vì
                        vậy chúng tôi không chịu trách nhiệm về bất kỳ thiệt hại nào phát sinh do trì hoãn trong việc gửi bởi công ty được ủy thác
                    </li>
                </ol>
            </div>
            <!-- article 20 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 20 (Bồi thường thiệt hại)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>Người sử dụng phải bồi thường cho công ty nếu vi phạm điều khoản trong thỏa thuận này hoặc gây thiệt hại cho công ty trong quá trình sử dụng dịch vụ</li>
                    <li>Phía công ty chúng tôi sẽ không chịu trách nhiệm bồi thường bất cứ thiệt hại nào mà người sử dụng hoặc bên thứ ba phải chịu đối với dịch vụ của công ty chúng tôi</li>
                </ol>
            </div>
            <!-- article 21 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 21 (Chuyển nhượng Thỏa thuận sử dụng này)</h3>
                <ol className="list-decimal ml-12 text-sm leading-6">
                    <li>
                        Nếu không có sự đồng ý bằng văn bản từ phía công ty, người dùng không thể chuyển nhượng, cho phép thừa kế, thế chấp hoặc thực hiện bất kỳ giao dịch nào liên quan đến quyền lợi
                        hoặc nghĩa vụ của người dùng hoặc tình trạng tài khoản dùng dịch vụ này cho bên thứ ba.
                    </li>
                    <li>
                        Trong trường hợp công ty chúng tôi chuyển nhượng hoạt động kinh doanh dịch vụ này cho một công ty khác, chúng tôi có thể chuyển nhượng quyền, nghĩa vụ, trạng thái tài khoản và
                        thông tin người dùng, cũng như mọi thông tin liên quan khác được quy định trong Điều 5 của điều khoản sử dụng này cho công ty khác và người dùng đồng ý trước với điều khoản này
                        về việc chuyển nhượng. Ngoài ra, chuyển nhượng doanh nghiệp nêu tại mục này bao gồm tất cả các trường hợp chia tách công ty và chuyển nhượng doanh nghiệp khác.
                    </li>
                </ol>
            </div>
            <!-- article 22 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 22 (tính phân tách)</h3>
                <p className="text-sm leading-6">
                    Nếu bất kỳ điều khoản nào của Thỏa thuận này hoặc một phần của nó được xác định là không hợp lệ hoặc không thể thi hành theo luật và quy định, thì các điều khoản còn lại của các
                    Điều khoản này và phần còn lại của bất kỳ điều khoản nào được coi là không hợp lệ hoặc không thể thi hành một phần vẫn tiếp tục có đầy đủ hiệu lực. Công ty của chúng tôi và người
                    dùng hợp pháp hóa điều khoản hoặc phần không hợp lệ hoặc không thể thi hành đó và sửa chữa nó trong phạm vi cần thiết để làm cho nó có thể thi hành được. Các nỗ lực sẽ được thực
                    hiện để đảm bảo mục đích và hiệu quả tương đương về mặt pháp lý và kinh tế của các điều khoản hoặc phần không hợp lệ hoặc không thể thi hành đó.
                </p>
            </div>
            <!-- article 23 -->
            <div>
                <h3 className="font-semibold text-base leading-8">Điều 23 (giải pháp thương lượng)</h3>
                <p className="text-sm leading-6">
                    Đối với các vấn đề không được quy định trong thỏa thuận này hoặc các vấn đề gây nghi ngờ về việc giải thích thỏa thuận này, khách hàng và Công ty sẽ tham khảo ý kiến ​​​​một cách
                    thiện chí và cùng giải quyết.
                </p>
            </div>
            <!-- end -->
            <div className="text-right text-sm my-8">
                <p>Ban hành: 07 tháng 03 năm 2023</p>
                <p>Sửa đổi lần cuối: ngày 7 tháng 3 năm 2023</p>
            </div>
        </div>
    </div>
    <Footer class="mt-auto" />

</template>
