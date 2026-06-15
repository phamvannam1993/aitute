<?php

namespace Database\Seeders;

use App\Models\CreatomateTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreatomateMaxLengthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UpdateCreatomateMaxLengthSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Danh sách template_id và structure tương ứng
            $templates = [
                // Template 1
                'bdc52cd3-043d-4416-a3df-772866268c02' => '{
                    "composition": {
                        "Alertmessage": {
                            "value": "",
                            "type": "text",
                            "label": "Thông báo",
                            "length": "25"
                        },
                        "Currentprice": {
                            "value": "",
                            "type": "text",
                            "label": "Giá bán hiện tại",
                            "length": "20"
                        },
                        "Originalprice": {
                            "value": "",
                            "type": "text",
                            "label": "Giá gốc",
                            "length": "20"
                        },
                        "Itemdescription": {
                            "value": "",
                            "type": "text",
                            "label": "Mô tả sản phẩm",
                            "length": "70"
                        },
                        "Itemtitle": {
                            "value": "",
                            "type": "text",
                            "label": "Tên sản phẩm",
                            "length": "50"
                        },
                        "Promotext": {
                            "value": "",
                            "type": "text",
                            "label": "Thông điệp khuyến mãi",
                            "length": "15"
                        },
                        "ItemImage": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm"
                        },
                        "Companywebsite": {
                            "value": "",
                            "type": "text",
                            "label": "Website công ty",
                            "length": "40"
                        },
                        "Actionmessage": {
                            "value": "",
                            "type": "text",
                            "label": "Lời kêu gọi hành động",
                            "length": "50"
                        },
                        "Background": {
                            "value": "",
                            "type": "image",
                            "label": "Hình nền"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 2
                '12c40f00-5094-41e4-ade4-b1ddc7df2a02' => '{
                    "composition": {
                        "Itemdescription": {
                            "value": "",
                            "type": "text",
                            "label": "Mô tả sản phẩm",
                            "length": "30"
                        },
                        "Productname": {
                            "value": "",
                            "type": "text",
                            "label": "Tên sản phẩm",
                            "length": "20"
                        },
                        "Itemtitle1": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề sản phẩm 1",
                            "length": "10"
                        },
                        "Itemtitle2": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề sản phẩm 2",
                            "length": "20"
                        },
                        "Slogan1": {
                            "value": "",
                            "type": "text",
                            "label": "Khẩu hiệu 1",
                            "length": "20"
                        },
                        "Itemimage1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 1"
                        },
                        "Itemimage2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 2"
                        },
                        "Slogan2": {
                            "value": "",
                            "type": "text",
                            "label": "Khẩu hiệu 2",
                            "length": "50"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 3
                'b1e5a107-b865-4517-bc31-a43a820c3be1' => '{
                    "composition": {
                        "Time": {
                            "value": "",
                            "type": "text",
                            "label": "Thời gian",
                            "length": "15"
                        },
                        "Brandname1": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 1",
                            "length": "10"
                        },
                        "Itemtitle1": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề sản phẩm 1",
                            "length": "15"
                        },
                        "Brandname2": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 2",
                            "length": "15"
                        },
                        "Itemtitle2": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề sản phẩm 2",
                            "length": "10"
                        },
                        "Promotext": {
                            "value": "",
                            "type": "text",
                            "label": "Nội dung khuyến mãi",
                            "length": "10"
                        },
                        "Itemimage1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 1"
                        },
                        "Itemimage2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 2"
                        },
                        "Productoffer1": {
                            "value": "",
                            "type": "text",
                            "label": "Ưu đãi sản phẩm 1",
                            "length": "8"
                        },
                        "Productimage1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh ưu đãi 1"
                        },
                        "Productoffer2": {
                            "value": "",
                            "type": "text",
                            "label": "Ưu đãi sản phẩm 2",
                            "length": "8"
                        },
                        "Productimage2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh ưu đãi 2"
                        },
                        "Productoffer3": {
                            "value": "",
                            "type": "text",
                            "label": "Ưu đãi sản phẩm 3",
                            "length": "8"
                        },
                        "Productimage3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh ưu đãi 3"
                        },
                        "Website": {
                            "value": "",
                            "type": "text",
                            "label": "Trang web",
                            "length": "50"
                        },
                        "Brandname3": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 3",
                            "length": "15"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 4
                '8a55463b-4d35-4d6e-ad6a-f56636f37d42' => '{
                    "composition": {
                        "Subjectmessage": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề thông điệp",
                            "length": "75"
                        },
                        "Introductorymessage": {
                            "value": "",
                            "type": "text",
                            "label": "Thông điệp giới thiệu",
                            "length": "47"
                        },
                        "Text": {
                            "value": "",
                            "type": "text",
                            "label": "Nội dung văn bản",
                            "length": "17"
                        },
                        "Image": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh"
                        },
                        "Video": {
                            "value": "",
                            "type": "video",
                            "label": "Video"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 5
                '9c4dc50a-c891-49b3-8cd9-7d280a292fee' => '{
                    "composition": {
                        "Link": {
                            "value": "",
                            "type": "text",
                            "label": "Liên kết",
                            "length": "35"
                        },
                        "Contentmessage1": {
                            "value": "",
                            "type": "text",
                            "label": "Thông điệp nội dung 1",
                            "length": "52"
                        },
                        "Contentmessage2": {
                            "value": "",
                            "type": "text",
                            "label": "Thông điệp nội dung 2",
                            "length": "36"
                        },
                        "Openingmessage1": {
                            "value": "",
                            "type": "text",
                            "label": "Lời mở đầu 1",
                            "length": "23"
                        },
                        "Background": {
                            "value": "",
                            "type": "image",
                            "label": "Hình nền"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 6
                '45f83383-5507-4643-a5d2-40e86060c429' => '{
                    "composition": {
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 1"
                        },
                        "Slogan1": {
                            "value": "",
                            "type": "text",
                            "label": "Slogan 1",
                            "length": "30"
                        },
                        "BrandName1": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 1",
                            "length": "15"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 2"
                        },
                        "Slogan2": {
                            "value": "",
                            "type": "text",
                            "label": "Slogan 2",
                            "length": "50"
                        },
                        "BrandName2": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 2",
                            "length": "15"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 3"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 7
                'fa278d01-443e-4522-86f6-7d5aab9987ad' => '{
                    "composition": {
                        "Time": {
                            "value": "",
                            "type": "text",
                            "label": "Thời gian khuyến mãi",
                            "length": "20"
                        },
                        "Title": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề",
                            "length": "15"
                        },
                        "Sale": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin giảm giá",
                            "length": "8"
                        },
                        "Brandname": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu",
                            "length": "20"
                        },
                        "Brandimage1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh thương hiệu 1"
                        },
                        "Brandimage2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh thương hiệu 2"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 8
                '22d6f0eb-48f1-4aef-af6d-c0ad81e377b0' => '{
                    "composition": {
                        "Productname": {
                            "value": "",
                            "type": "text",
                            "label": "Tên sản phẩm",
                            "length": "12"
                        },
                        "Buy": {
                            "value": "",
                            "type": "text",
                            "label": "Nút mua hàng",
                            "length": "10"
                        },
                        "Brandname": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu",
                            "length": "15"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 1"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 2"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 3"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 9
                'ddabbf5f-6f81-4a4c-846e-05337b5a3258' => '{
                    "composition": {
                        "Productname": {
                            "value": "",
                            "type": "text",
                            "label": "Tên sản phẩm",
                            "length": "20"
                        },
                        "Brandname": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu",
                            "length": "15"
                        },
                        "Details": {
                            "value": "",
                            "type": "text",
                            "label": "Chi tiết sản phẩm",
                            "length": "40"
                        },
                        "Price": {
                            "value": "",
                            "type": "text",
                            "label": "Giá sản phẩm",
                            "length": "16"
                        },
                        "Buynow": {
                            "value": "",
                            "type": "text",
                            "label": "Nút mua ngay",
                            "length": "10"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 1"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 2"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 3"
                        },
                        "Image4": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 4"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 10
                'ef6087f8-53bd-462e-90b5-91d1669c3083' => '{
                    "composition": {
                        "Slogan1": {
                            "value": "",
                            "type": "text",
                            "label": "Câu khẩu hiệu 1",
                            "length": "20"
                        },
                        "Slogan2": {
                            "value": "",
                            "type": "text",
                            "label": "Câu khẩu hiệu 2",
                            "length": "45"
                        },
                        "Brandname1": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 1",
                            "length": "15"
                        },
                        "Brandname2": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 2",
                            "length": "15"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 1"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 2"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 3"
                        },
                        "Image4": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 4"
                        },
                        "Image5": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm 5"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 11
                '2204ec08-df2f-4625-b4f0-cfc3ec9ab93a' => '{
                    "composition": {
                        "Content1": {
                            "value": "",
                            "type": "text",
                            "label": "Thông điệp nội dung 1",
                            "length": "223"
                        },
                        "Content2": {
                            "value": "",
                            "type": "text",
                            "label": "Thông điệp nội dung 2",
                            "length": "240"
                        },
                        "Background1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình nền 1"
                        },
                        "Background2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình nền 2"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 12
                '05b908a5-51ac-4e06-a3ed-f30e9830ca89' => '{
                    "composition": {
                        "Comment": {
                            "value": "",
                            "type": "text",
                            "label": "Bình luận",
                            "length": "35"
                        },
                        "Date": {
                            "value": "",
                            "type": "text",
                            "label": "Ngày",
                            "length": "36"
                        },
                        "Name": {
                            "value": "",
                            "type": "text",
                            "label": "Tên",
                            "length": "145"
                        },
                        "Profilepicture": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh đại diện"
                        },
                        "Photo1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh sản phẩm"
                        },
                        "Photo2": {
                            "value": "",
                            "type": "image",
                            "label": "Ảnh nền video"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 13
                '8edd268b-5fea-49ff-bb7f-e7c9343c0e64' => '{
                    "composition": {
                        "Nameoffirstimage": {
                            "value": "",
                            "type": "text",
                            "label": "Tên hình ảnh 1",
                            "length": "59"
                        },
                        "Nameofthesecondimage": {
                            "value": "",
                            "type": "text",
                            "label": "Tên hình ảnh 2",
                            "length": "60"
                        },
                        "Thirdimagename": {
                            "value": "",
                            "type": "text",
                            "label": "Tên hình ảnh 3",
                            "length": "60"
                        },
                        "Firstintroductorymessage": {
                            "value": "",
                            "type": "text",
                            "label": "Tin nhắn giới thiệu 1",
                            "length": "78"
                        },
                        "Secondintroductorymessage": {
                            "value": "",
                            "type": "text",
                            "label": "Tin nhắn giới thiệu 2",
                            "length": "78"
                        },
                        "Thirdintroductorymessage": {
                            "value": "",
                            "type": "text",
                            "label": "Tin nhắn giới thiệu 3",
                            "length": "78"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 1"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 2"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 3"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 14
                '4131722f-1416-46cd-bfdc-b113cc0c93cc' => '{
                    "composition": {
                        "Name": {
                            "value": "",
                            "type": "text",
                            "label": "Tên",
                            "length": "18"
                        },
                        "Message1": {
                            "value": "",
                            "type": "text",
                            "label": "Tin nhắn 1",
                            "length": "65"
                        },
                        "Message2": {
                            "value": "",
                            "type": "text",
                            "label": "Tin nhắn 2",
                            "length": "60"
                        },
                        "Image": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 15
                'c15c99ed-bedc-4fb9-802c-4a11251be359' => '{
                    "composition": {
                        "Name1": {
                            "value": "",
                            "type": "text",
                            "label": "Nội dung 1",
                            "length": "30"
                        },
                        "Name2": {
                            "value": "",
                            "type": "text",
                            "label": "Nội dung 2",
                            "length": "30"
                        },
                        "Name3": {
                            "value": "",
                            "type": "text",
                            "label": "Nội dung 3",
                            "length": "30"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 1"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 2"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 3"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 17
                '79d44253-a167-402d-8fc9-889c994f1adc' => '{
                    "composition": {
                        "Title": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề",
                            "length": "60"
                        },
                        "Brandname": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu",
                            "length": "12"
                        },
                        "Productescription": {
                            "value": "",
                            "type": "text",
                            "label": "Mô tả sản phẩm",
                            "length": "220"
                        },
                        "Productname1": {
                            "value": "",
                            "type": "text",
                            "label": "Tên sản phẩm 1",
                            "length": "20"
                        },
                        "Productprice1": {
                            "value": "",
                            "type": "text",
                            "label": "Giá sản phẩm 1",
                            "length": "20"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 1"
                        },
                        "Productname2": {
                            "value": "",
                            "type": "text",
                            "label": "Tên sản phẩm 2",
                            "length": "20"
                        },
                        "Productprice2": {
                            "value": "",
                            "type": "text",
                            "label": "Giá sản phẩm 2",
                            "length": "20"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 2"
                        },
                        "Productname3": {
                            "value": "",
                            "type": "text",
                            "label": "Tên sản phẩm 3",
                            "length": "20"
                        },
                        "Productprice3": {
                            "value": "",
                            "type": "text",
                            "label": "Giá sản phẩm 3",
                            "length": "20"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 3"
                        },
                        "Productname4": {
                            "value": "",
                            "type": "text",
                            "label": "Tên sản phẩm 4",
                            "length": "20"
                        },
                        "Productprice4": {
                            "value": "",
                            "type": "text",
                            "label": "Giá sản phẩm 4",
                            "length": "20"
                        },
                        "Image4": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 4"
                        },
                        "Logo": {
                            "value": "",
                            "type": "image",
                            "label": "Logo"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Nhạc nền"
                        }
                    }
                }',
                // Template 18
                '603dbc7b-ba97-4a4b-88ec-b4c15accad6f' => '{
                    "composition": {
                        "Name": {
                            "value": "",
                            "type": "text",
                            "label": "Tên",
                            "length": "20"
                        },
                        "Contact": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin liên hệ",
                            "length": "30"
                        },
                        "Avatar": {
                            "value": "",
                            "type": "image",
                            "label": "Ảnh đại diện"
                        },
                        "Title1": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề 1",
                            "length": "10"
                        },
                        "Title2": {
                            "value": "",
                            "type": "text",
                            "label": "Tiêu đề 2",
                            "length": "10"
                        },
                        "Information1": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin 1",
                            "length": "40"
                        },
                        "Information2": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin 2",
                            "length": "40"
                        },
                        "Information3": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin 3",
                            "length": "40"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 1"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 2"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 3"
                        },
                        "Image4": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 4"
                        },
                        "Image5": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 5"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Nhạc nền"
                        }
                    }
                }',
                // Template 19
                'd29e06ac-3131-48ab-a75d-88453f78a3e8' => '{
                    "composition": {
                        "Slogan": {
                            "value": "",
                            "type": "text",
                            "label": "Khẩu hiệu",
                            "length": "20"
                        },
                        "Logo": {
                            "value": "",
                            "type": "image",
                            "label": "Logo"
                        },
                        "Brandname1": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 1",
                            "length": "10"
                        },
                        "Brandname2": {
                            "value": "",
                            "type": "text",
                            "label": "Tên thương hiệu 2",
                            "length": "10"
                        },
                        "Productdescription1": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin sản phẩm 1",
                            "length": "35"
                        },
                        "Productdescription2": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin sản phẩm 2",
                            "length": "35"
                        },
                        "Productdescription3": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin sản phẩm 3",
                            "length": "35"
                        },
                        "Productdescription4": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin sản phẩm 4",
                            "length": "35"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 1"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 2"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 3"
                        },
                        "Image4": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 4"
                        },
                        "Image5": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 5"
                        },
                        "Image6": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 6"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
                // Template 22
                'ec0b82a5-3adb-448d-bf7a-ece855feb587' => '{
                    "composition": {
                        "Document1": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin 1",
                            "length": "66"
                        },
                        "Document2": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin 2",
                            "length": "75"
                        },
                        "Document3": {
                            "value": "",
                            "type": "text",
                            "label": "Thông tin 3",
                            "length": "75"
                        },
                        "Image1": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 1"
                        },
                        "Image2": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 2"
                        },
                        "Image3": {
                            "value": "",
                            "type": "image",
                            "label": "Hình ảnh 3"
                        },
                        "Audio": {
                            "value": "",
                            "type": "audio",
                            "label": "Âm thanh"
                        }
                    }
                }',
            ];

            // Lặp qua từng template_id và cập nhật
            foreach ($templates as $templateId => $structure) {
                $template = CreatomateTemplate::where('template_id', $templateId)->first();

                if ($template) {
                    $template->structure = $structure;
                    $template->save();
                    echo "Đã cập nhật template với ID: $templateId\n";
                } else {
                    echo "Không tìm thấy template với ID: $templateId\n";
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
