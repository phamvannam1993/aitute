<template>

  <Head title="Hình ảnh - AI 3 GỐC - Cộng đồng AI tử tế" />
  <div>
    <h1 class="text-center mb-10 text-3xl font-bold">Tạo App Facebook</h1>

    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 1. Đăng ký tài khoản Developer</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Truy cập vào trang <a class="text-blue-600" href="https://developers.facebook.com/async/registration/"
                target="_blank">https://developers.facebook.com/async/registration/</a> và đăng ký tài khoản Developer.
            </p>
          </li>
          <li>
            <p>Chọn <strong>“Log in with Facebook ”</strong></p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-1.png" />
    </div>

    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 2. Tạo App</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Sau khi hoàn tất đăng ký, bạn sẽ được chuyển hướng đến trang <strong> Tạo ứng dụng mới </strong> hoặc
              truy cập vào <a class="text-blue-600" href="https://developers.facebook.com/apps" target="_blank"
                rel="noopener noreferrer">https://developers.facebook.com/apps/</a></p>
          </li>
          <li>
            <p>Nhấn nút <strong>Create App</strong> ở góc trên bên phải.</p>
          </li>
          <p><strong>Lưu ý: </strong>Các ứng dụng đã được tạo trước đây hoặc các ứng dụng cũ sẽ không còn hoạt động.</p>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-2.png" />
    </div>

    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 3. Điền thông tin</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Điền thông tin và nhấn <strong>“Next”</strong> </p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-3.png" />
    </div>

    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 4. Chọn tính năng App</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Chon Other và nhấn <strong>“Next”</strong> </p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-4.png" />
    </div>


    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 5. Chọn kiểu App</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Chọn <strong>Business</strong> và nhấn <strong>“Next”</strong> </p>
          </li>
          <p><strong>Lưu ý: </strong>Xác nhận chính xác đã chọn <strong>Business</strong> nếu chọn sai ứng dụng sẽ không
            hoạt động được</p>

        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-5.png" />
    </div>


    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 6. Xác nhận thông tin</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Xác nhận thông tin và nhấn <strong>“Create App”</strong> </p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-6.png" />
    </div>

    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 7. Thêm Product</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Chọn <strong>“Add Product”</strong> </p>
          </li>
          <li class="">
            <p>Tìm <strong>“Facebook Login for Business”</strong> </p>
          </li>
          <li class="">
            <p>Chọn <strong>“Set up”</strong> </p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-7.png" />
    </div>


    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 8. Cài đặt Product</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Chọn <strong>“Setting”</strong> </p>
          </li>
          <li class="">
            <p>Tìm đến <strong>“Valid OAuth Redirect URIs”</strong> </p>
          </li>
          <li class="">
            <p>Dán vào: <strong class="text-orion-sec">{{ route('social.facebook.callback-access-token') }}</strong>
            </p>
          </li>
          <li class="">
            <p>Chọn <strong>“Save changes”</strong> </p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-8.png" />
    </div>

    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 9. Cài đặt App</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Chọn <strong>“Basic”</strong> </p>
          </li>
          <li class="">
            <p>Điền thông tin <strong>“App domains”</strong>: <strong class="text-orion-sec">{{ domain }}</strong></p>
          </li>
          <li class="">
            <p>Điền thông tin <strong>“Privacy Policy URL”</strong>: <strong class="text-orion-sec">{{
              route('rules.privacy') }}</strong></p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-9.png" />
    </div>

    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 10. Sao chép thông tin</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Sao chép <strong>“App ID”</strong> </p>
          </li>
          <li class="">
            <p>Sao chép <strong>“App secret”</strong></p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-10.png" />
    </div>

    <div class="mb-8">
      <h2 class="font-bold text-2xl mb-4">Bước 11. Lưu thông tin</h2>
      <div class="mb-4">
        <ul class="list-disc pl-5 space-y-2">
          <li class="">
            <p>Truy cập vào: <strong><a class="text-blue-600" :href="route('social.facebook.create')">{{
              route('social.facebook.create')
                  }}</a></strong> </p>
          </li>
          <li class="">
            <p>Dán <strong>“App ID”</strong> và <strong>“App secret”</strong> vào trường tương ứng </p>
          </li>
          <li class="">
            <p>Nhấn <strong>“Lưu”</strong> </p>
          </li>
        </ul>
      </div>
      <img class="w-10/12 text-center m-auto" src="/assets/img/facebook/step-11.png" />
    </div>

  </div>
</template>
<script setup>
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from 'vue';
const domain = ref(window.location.hostname);
defineOptions({ layout: Layout });

</script>
