<template>
    <div class="w-full px-2">
        <div class="justify-between items-center lg:gap-[40px] pt-[2px] lg:pt-[4px]">
            <div class="md:float-left mr-4">
                 <iframe
                                class="w-full md:w-[368px] h-auto md:h-[207px] aspect-video rounded-lg"
                                :src="introVideo"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen>
                 </iframe>
            </div>


            <div class="flex-1 mt-4 md:mt-0">
                <p class="text-[14px] md:text-base text-orion-primary text-justify">
                    Chào các bạn, tôi là Hiếu Orion, đây là 1 sản phẩm mà tôi ấp ủ từ lâu: ORION AI là một hệ thống ứng dụng AI với mục đích giúp người Bán hàng có thể:
                </p>
                <p class="text-[14px] md:text-base text-orion-primary text-justify">
                   1) Thu hút khách hàng dễ dàng hơn.
                </p>
                 <p class="text-[14px] md:text-base text-orion-primary text-justify">
                   2) Bán được hàng nhiều hơn
                </p>
                <p class="text-[14px] md:text-base text-orion-primary text-justify">
                    3) Tiết kiệm chi phí hơn.
                </p>
                <p class="text-[14px] md:text-base text-orion-primary text-justify">
                    Orion AI không phải chỉ là 1 công cụ tạo nội dung AI đâu nhé! Nó chính là 1 Giải pháp Truyền thông và Bán Hàng đặc biệt cho bạn. Nó tạo ra một Hành Trình Bán Hàng đầy đủ cho bạn: Từ việc phân tích Sản Phẩm > Xây dựng chiến dịch truyền thông > Tạo ra nội dung truyền thông dưới nhiều dạng (Text, Ảnh, Nhạc, Video) > Xuất bản ra Kênh truyền thông của bạn (Fanpage) > Tìm kiếm khách hàng > Xây dựng Thương hiệu cá nhân > Xây dựng Chatbot trò chuyện với khách hàng > Chốt sales bán hàng.
               </p>
                 <p class="text-[14px] md:text-base text-orion-primary text-justify">
                    Và đây là công cụ duy nhất ở Việt Nam có hệ thống AI Agents: Tự tạo sinh ra Nội dung mỗi ngày và tự đăng lên hệ thống kênh Fanpage của bạn. Mà bạn không cần phải làm gì.
                 </p>
                  <p class="text-[14px] md:text-base text-orion-primary text-justify">
                    Đặc biệt là nó cực đơn giản và bất kỳ ai chỉ cần biết chữ và biết bấm nút là có thể làm được
                  </p>
            </div>
        </div>

        <!-- Tiêu đề danh sách gói ứng dụng -->
        <div class="w-full flex items-center justify-between my-3 lg:my-6 px-2">
            <div class="flex items-center gap-3">
                <img src="/assets/img/orion-1.png" alt="step" class="w-[22px] h-auto" />
                <p class="text-[15px] md:text-lg font-bold text-orion-primary">Hãy lựa chọn gói ứng dụng phù hợp cho mình</p>
            </div>
        </div>

        <!-- Danh sách các gói ứng dụng -->
        <div v-for="(packageItem, index) in appPackages"
            :key="index"
            class="flex flex-col md:flex-row items-center gap-3 lg:gap-6 mb-8 p-2 rounded-lg shadow-md bg-white lg:bg-transparent lg:shadow-none">

            <!-- Banner và mô tả -->
            <a :href="packageItem.url" target="_blank" class="items-start md:rounded-lg md:shadow-md p-0 md:p-4 gap-2 lg:gap-4 lg:min-h-[198px] bg-white">

                <img :src="packageItem.banner" :alt="'Banner ' + packageItem.title"
                    class="float-left w-auto md:w-64 h-[104px] md:h-full rounded-lg shadow-md object-cover mr-4">

                <div class="gap-2 h-full">
                    <div class="">
                        <p v-html="packageItem.overview" :class="`text-[12px] lg:text-[16px] text-black text-justify`">
                        </p>
                    </div>

                    <div class="flex items-center justify-end">
                        <a :href="packageItem.url" target="_blank"
                        class="flex items-center gap-2 text-orion-primary transition italic hover:text-orion-sec text-[12px] lg:text-[16px] cursor-pointer">
                            Xem chi tiết
                            <img src="/assets/img/orion/icon/label-arrow.svg" alt="step" class="w-[20px] h-auto" />
                        </a>
                    </div>
                </div>
            </a>

            <!-- <div class="w-full md:w-[352px] h-auto aspect-video rounded-lg overflow-hidden">
                <template v-if="packageItem.youtubeLink">
                    <iframe
                        class="w-full h-full"
                        :src="packageItem.youtubeLink"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>
                </template>
                <template v-else>
                    <img
                        src="/assets/img/orion/app_packages/empty_ytb.png"
                        alt="No video available"
                        class="w-full h-full object-cover"
                    />
                </template>
            </div> -->

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const introVideo = ref("https://www.youtube.com/embed/Z42o713WVzU?si=tXCQBu4SCKOvGe6n");

const appPackages = ref([
    {
        title: "Gói Trải Nghiệm",
        banner: "/assets/img/orion/app_packages/trial.png",
        overview: "Gói này là 1 tháng Trải nghiệm đầu tiên cho những người muốn tìm hiểu ORION AI : bạn sẽ được dùng TẤT CẢ các ứng dụng trên hệ thống Orion AI - sướng nhé! Nhưng do là gói Trải Nghiệm nên bạn sẽ bị hạn chế: Thời hạn sử dụng (chỉ 15 ngày - vừa đủ đẻ bạn hiểu về ORION AI và đăng ký các gói cao hơn), Ngoài ra, với 1 số ứng dụng quan trọng, bạn sẽ bị giới hạn lần sử dụng.  <br> <span class='font-bold'> (*) Chú ý:</span>  Gói này bạn cũng được cho 1 lượng Điểm ORION đủ để làm việc bét nhè - nhưng nếu Hết thời hạn sử dụng (15 ngày) thì bạn có thể nâng lên gói trên mà vẫn giữ được nguyên Điểm còn lại của bạn. ",
        url: import.meta.env.VITE_PACKAGE_TRIAL,
        infoAdd: ''
    },
    {
        title: "Gói Tiêu Chuẩn",
        banner: "/assets/img/orion/app_packages/standard.png",
        overview: "Đây là gói cơ bản nhất với những ứng dụng cần thiết nhất cho 1 người Bán Hàng: Bạn sẽ có được các công cụ vừa đủ để tạo ra nội dung bán hàng một cách dễ dàng và đơn giản nhất, tiết kiệm nhất. Nếu bạn là người bắt đầu bán hàng và công việc không nhiều, thì tôi khuyên bạn nên sử dụng gói này. Bạn sẽ hoàn toàn hài lòng với công cụ và cả giá cả - nó chỉ bằng 1 phần 10 chi phí mà đúng ra bạn sẽ phải bỏ ra cho những công cụ AI trên thị trường.  ",
        url: import.meta.env.VITE_PACKAGE_STANDARD,
        infoAdd: ''
    },
    {
        title: "Gói Nâng Cao",
        banner: "/assets/img/orion/app_packages/advanced.png",
        overview: "Đây là gói Chuyên nghiệp nâng cao dành cho những người muốn có nhiều công cụ phức tạp hơn, và có nhiều tài nguyên sử dụng hơn. Gói NÂNG CAO này phù hợp với những người Bán Hàng cần xây dựng Thương hiệu Chuyên Nghiệp, hoặc những người làm nghề Content, Truyền thông, Marketing cần có công cụ chuyên sâu. <br>" +
         "Với gói Nâng Cao này, bạn sẽ có cả 1 hệ thống AI Agents tự động tạo ra 365 content suốt 1 năm và tự động đăng tải lên Fanpage của bạn mà bạn không phải làm gì. <br> Nếu bạn đang bán hàng hiệu quả, thì nên đăng ký gói này - nó có đầy đủ các công cụ AI hiện đại nhất để bạn thực hiện được mục tiêu bán hàng - và nó tự động hóa mọi thứ để bạn có thể tiết kiệm thời gian cho các việc khác.",
        url: import.meta.env.VITE_PACKAGE_ADVANCED,
        infoAdd: ''
    },
    {
        title: "Gói Chuyên Sâu",
        banner: "/assets/img/orion/app_packages/professional.png",
        overview: "Gói này là gói đặc biệt dành cho những người muốn được hỗ trợ tất cả một cách Chi tiết nhất. Với gói Chuyên Sâu này, bạn sẽ được sử dụng tất cả các ứng dụng hay ho nhất của ORION AI, và được hỗ trợ nhiệt tình bởi các Chuyên Gia. <br>"+
                   "Gói Chuyên sâu có các hệ thống phân tích cho Doanh nghiệp dựa trên thực trạng thương hiệu - từ đó ra 1 Chiến lược triển khai nội dung phù hợp hiệu quả: <br> Ngoài ra có 1 sự đặc biệt của hệ thống Chuyên Sâu, đó là hệ thống Chatbot riêng cho 1 doanh nghiệp, có thể giúp bạn có thêm 1 hệ thống tiếp cận khách hàng tuyệt vời - và kỳ diệu nữa là bạn sẽ có có thể tự lam và add vào Fanpage của bạn - sau đó bạn sẽ có cả hệ thống thống kê các Khách Hàng sẽ vào trò chuyện trên đó! <br> Nếu bạn là 1 người đang bán hàng doanh số lớn - hoặc muốn bán hàng với doanh số lớn - thì bạn nên sử dụng gói này vì nó có vô vàn các ứng dụng hỗ trợ hiệu quả cho bạn trong việc tìm kiếm khách hàng, tác động cảm xúc của họ để họ mua hàng, và tự động hóa mọi thứ để bạn có thể rảnh tay , và muốn có được sự hỗ trợ Mentor tốt nhất từ con người đến công cụ để hỗ trợ cả Thương hiệu cá nhân lẫn Mô hình kinh doanh chuyên nghiệp - thì đây chính là lựa chọn cho bạn! ",
        infoAdd: '',
        url: import.meta.env.VITE_PACKAGE_PROFESSIONAL
    }
]);

</script>
