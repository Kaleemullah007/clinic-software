<?php

return [
    'name' => 'Dr. Mubashir Mushtaq Daha',
    'email' => 'drmubashirmushtaqdaha@gmail.com',
    'phone' => '+923335560509',
    'address' => 'Skin Laser and Dental Clinic ,  Jinnah Super Market ( F-7 Markaz ) Near Saeed Book Bank , Islamabad.',
    'logo' => '/assets/images/logo.png',
    'message' => 'Thank you for your request for contact us.',

    'slider' => [
        'assets/images/general-derm.png',
        'assets/images/dermatology.png',
    ],

    'mobile_slider' => [
        'assets/images/mob-slider1.png',
        'assets/images/mob-slider2.png',
        'assets/images/mob-slider3.png',
        'assets/images/mob-slider4.png',
    ],

    'email_forms' => [
        'contact_us' => [
            'from' => 'info@upliftcom.com',
            'message' => 'Thank you for your request for contact us.',
            'mailto' => ['kaleemullahdev@gmail.com'],
            'subject' => 'Contact us Information Request',
            'email_template' => 'email.contact-us',
            'completed_view' => 'email.contact-us-thankyou',
            'validation' => [
                'rules' => [
                    'name'      => 'required',
                    'subject'      => 'required',
                    'email'     => 'required|email',
                    'message'   => 'required',
                    'phone'   => 'sometimes|required|numeric|digits_between:9,11',
                    'txtSpamCode' => [
                        'required',
                        'Rule' => [
                            'recaptcha' => 'ValidReCaptcha',
                        ],
                    ],
                ],
                // 'messages' => [
                //     'g-recaptcha-response.required' => 'Please check the I\'m not a robot box',
                // ],
            ],
        ],

        'appointment' => [
            'from' => 'info@upliftcom.com',
            'message' => 'Thank you for your request for appointment.',
            'mailto' => ['abcd@gmail.com'],
            'subject' => 'Appointment Information Request',
            'email_template' => 'email.apointment',
            'completed_view' => 'email.apointment-thankyou',
            'validation' => [
                'rules' => [
                    'name'      => 'required',
                    'service'      => 'required',
                    'phone'      => 'required|numeric|digits_between:9,11',

                    'email'     => 'sometimes|email|unique:users',
                    'date'   => 'required',
                    'time'   => 'required',
                    'txtSpamCode' => [
                        'required',
                        'Rule' => [
                            'recaptcha' => 'ValidReCaptcha',
                        ],
                    ],
                ],
                // 'messages' => [
                //     'g-recaptcha-response.required' => 'Please check the I\'m not a robot box',
                // ],
            ],
        ]
    ],
    'pages' => [
        'prp-for-hair-regrowth'  => [
            'heading' => 'PRP for Hair Regrowth',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/prp-for-hair-regrowth.png',
                    'alt' => 'PRP for Hair Regrowth',
                    'link' => 'prp-for-hair-regrowth',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'PRP for Hair Regrowth',
                    'class' => 'card-img-top',
                    'is_discount'=>true,
                    'price' => 'Rs. 17000/- Per Session',
                    'discounted_price' => 'Rs. 16000/- Per Session',
                ],
            ],
            'is_discount'=>true,
            'price' => 'Rs. 17000/-',
            'discounted_price' => 'Rs. 16000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Platelets Rich Plasma (PRP) is prepared from patient’s own blood and is injected on Scalp, Growth Factors and Serums are also added to enhance the results. It has wonderful results for hair regrowth and to reduce hair fall.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 17000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '02 Hours'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => '04 To 05'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'hair-transplant'  => [
            'heading' => 'Hair Transplant',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/hair-transplant.png',
                    'alt' => 'Hair Transplant',
                    'link' => 'hair-transplant',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Hair Transplant',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Not Given',
                    'discounted_price' => '',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Not Given',
            'discounted_price' => '',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Receiving a hair transplant can improve your appearance and self-confidence. Good candidates for a hair transplant include: </br></br>
                    1: men with male pattern baldness.</br>
                    2: women with thinning hair.</br>
                    3: anyone who has lost some hair from a burn or scalp injury.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Follicular Unit Extraction (F.U.E)',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'No Stitch',
                        'value' => ''
                    ],
                    '3' => [
                        'name' => 'Most Economical Procedure Is Taken In The Hand Of Most Competent Team',
                        'value' => ''
                    ],
                    '4' => [
                        'name' => 'PRP (Platelet Rich Plasma) for Scalp is also added to enhance the results.',
                        'value' => ''
                    ],
                ],
            ],
            'buttons' => [
                "0" => [
                    'text' => 'Make Appointment',
                    'href' => 'appointment',
                    'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                    'active' => true,
                ]
            ],
        ],
        'laser-hair-removal'  => [
            'heading' => 'Laser Hair Removal',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/laser-hair-removal.png',
                    'alt' => 'Laser Hair Removal',
                    'link' => 'laser-hair-removal',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Laser Hair Removal',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Not Given',
                    'discounted_price' => '',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Not Given',
            'discounted_price' => '',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Laser hair removal is the process of hair removal by means of exposure to pulses of laser light that destroy the hair follicle. It is being performed since many years.  Laser hair removal is widely practiced in clinics and is considered the safest and secure method to reduce the hair growth on the different parts of body.
                The females are more concerned about their hair growth especially on face and on other parts of body as well. Laser hair removal has wonderful results to reduce hair growth.</br>
                Laser Machine: - CANDELA N-D YAG LASER'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Cost Depends On The Part Of The Body'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => 'Time Depends On The Part Of The Body'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => '4-5 Session Reduce 80% Hair Growth'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '01 Month'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
            'table' => [
                'heading' => 'CANDELA N-D YAG Laser Hair Removal',
                'data' => [
                    ['Serial No.' , 'Services (Parts of Body)' , 'Price (PKR)' ],
                    ['1' , 'Full face & Neck' , '10000' ],
                    ['2' , 'Half Face' , '7000' ],
                    ['3' , 'Chin' , '3000' ],
                    ['4' , 'Full Arms' , '25000' ],
                    ['5' , 'Half Arms' , '15000' ],
                    ['6' , 'Under Arms' , '8000' ],
                    ['7' , 'Full legs' , '30000' ],
                    ['8' , 'Half legs' , '20000' ],
                    ['9' , 'Under Legs' , '15000' ],
                    ['10' , 'Male Beard Line (Top)' , '6000' ],
                    ['11' , 'Male Beard Line (Top & Neck )' , '8000' ],
                    ['12' , 'Full Body' , '100000' ],
                ],
            ]
        ],
        'co2-fractional-laser'  => [
            'heading' => 'CO2 Fractional Laser',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/co2-fractional-laser.png',
                    'alt' => 'CO2 Fractional Laser',
                    'link' => 'co2-fractional-laser',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'CO2 Fractional Laser',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 30000/-',
                    'discounted_price' => 'Rs. 30000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 30000/-',
            'discounted_price' => 'Rs. 30000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'The revolutionary CO2 fractional laser is a great treatment for those patients who are suffering from severe acne scars, deeper wrinkles, uneven tone and texture as well. It also offers the benefits of skin tightening, a smooth and even complexion, and a radiant glow with just one session.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 30000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '01 Hours'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The Depth & Density Of Scars'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => '01 Week'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'face-prp-micro-needlingandmesotherapy'  => [
            'heading' => 'PRP for Face + Micro Needling & Mesotherapy',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/face-prp-micro-needlingandmesotherapy.png',
                    'alt' => 'PRP for Face + Micro Needling & Mesotherapy',
                    'link' => 'face-prp-micro-needlingandmesotherapy',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'PRP for Face + Micro Needling & Mesotherapy',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 15000/-',
                    'discounted_price' => 'Rs. 15000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 15000/-',
            'discounted_price' => 'Rs. 15000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'If your problem is that your skin is looking very dull and lost that youthful glow that you once had or you have developed Acne Scars, then micro needling with platelet-rich plasma (PRP) therapy may be right for you. The procedure combines two therapies: micro needling, which stimulates collagen production, and PRP therapy, which uses a highly concentrated form of platelets from your own blood that contains immense regenerative and healing powers.</br>
                    We have found that by combining both therapies, patients receive much more dramatic effects. The benefits of PRP with micro needling are plenty – which is why more and more patients are signing up to see the phenomenal effects of this procedure for themselves. It has wonderful results for skin rejuvenation, skin glow, reduces pigmentation and to reduce acne scars. Serums are also added to get the maximum results.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 15000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '02 Hours'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends on The Depth Of The Scars'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => '48-72 Hours'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ],
            ],
        ],
        'acne-scar'  => [
            'co2-fractional-laser'=>'co2fractionallaser',
            'face-prp-micro-needlingandmesotherapy'=>'faceprpmicroneedlingandmesotherapy'
        ],

        'q-switch-laser'  => [
            'heading' => 'Q-Switch Laser',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/q-switch-laser.png',
                    'alt' => 'Q-Switch Laser',
                    'link' => 'q-switch-laser',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Q-Switch Laser',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 10000/-',
                    'discounted_price' => 'Rs. 10000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 10000/-',
            'discounted_price' => 'Rs. 10000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'This Q-switch Laser treatment is effective in treating freckles, sun spots, age spots, acne marks & melasma. It also gives hair, especially facial hair, a bleached effect.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 10000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '20 Minuets'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The Severity Of Pigmentation'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '15 Days To 01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'glutathione-cocktail'  => [
            'heading' => 'Glutathione Cocktail',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/glutathione-cocktail.png',
                    'alt' => 'Glutathione Cocktail',
                    'link' => 'glutathione-cocktail',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Glutathione Cocktail',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 15000/- per session',
                    'discounted_price' => 'Rs. 15000/- per session',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 15000/-',
            'discounted_price' => 'Rs. 15000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Glutathione Cocktail includes Glutathione, Vitamin C,D & E , Antioxidants, Anti Aging agents which is very effective for Skin Glow , Anti Aging and Immunity Boosting and to reduce hair fall etc.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'No Side Effects',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '3' => [
                        'name' => 'Works As A Immunity Booster And Anti Aging',
                        'value' => ''
                    ],
                    '4' => [
                        'name' => 'It Includes With C, D, E Collagen, Minerals And Anti Oxidant',
                        'value' => ''
                    ],
                    '5' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 15000/-'
                    ],
                    '6' => [
                        'name' => 'Procedure Time Required',
                        'value' => '30 Minuets'
                    ],
                    '7' => [
                        'name' => 'Total Session Required',
                        'value' => '10 Session (You Can See The Incredible Results In 06 To 07 Sessions)'
                    ],
                    '8' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '07  Days'
                    ],
                    '9' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'prp-micro-needling-and-mesotherapy'  => [
            'heading' => 'PRP + Micro Needling & Mesotherapy',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/prp-micro-needling-and-mesotherapy.png',
                    'alt' => 'PRP + Micro Needling & Mesotherapy',
                    'link' => 'prp-micro-needling-and-mesotherapy',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'PRP + Micro Needling & Mesotherapy',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 15000/-',
                    'discounted_price' => 'Rs. 15000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 15000/-',
            'discounted_price' => 'Rs. 15000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'If your problem is that your skin is looking very dull and lost that youthful glow that you once had or you have developed Acne Scars, then micro needling with platelet-rich plasma (PRP) therapy may be right for you. The procedure combines two therapies: micro needling, which stimulates collagen production, and PRP therapy, which uses a highly concentrated form of platelets from your own blood that contains immense regenerative and healing powers.</br>
                    We have found that by combining both therapies, patients receive much more dramatic effects. The benefits of PRP with micro needling are plenty – which is why more and more patients are signing up to see the phenomenal effects of this procedure for themselves. It has wonderful results for skin rejuvenation, skin glow, reduces pigmentation and to reduce acne scars. Serums are also added to get the maximum results.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 15000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '02 Hours'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The Severity Of Pigmentation'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => '02-03 Days'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'melasma-pigmentation-skin-glow'=>[
            'q-switch-laser'=>'qswitchlaser',
            'glutathione-cocktail'=>'glutathionecocktail',
            'prp-micro-needling-and-mesotherapy'=>'prpmicroneedlingandmesotherapy',
        ],

        'basic-simple-hydrafacial'  => [
            'heading' => 'Basic / Simple Hydrafacial',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/prp-micro-needling-and-mesotherapy.png',
                    'alt' => 'Basic / Simple Hydrafacial',
                    'link' => 'basic-simple-hydrafacial',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Basic / Simple Hydrafacial',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 8000/-',
                    'discounted_price' => 'Rs. 8000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 8000/-',
            'discounted_price' => 'Rs. 8000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'This is simple hydra facial which improves overall skin texture, tone, and appearance. This is due to the deep exfoliation that cleans your pores, removing debris and allowing for better penetration of face serums tailored to your skin type.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 8000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '30 Minuets'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The requirement of client'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'oxygeno-facial'  => [
            'heading' => 'Oxygeno Facial',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/oxygeno-facial.png',
                    'alt' => 'Oxygeno Facial',
                    'link' => 'oxygeno-facial',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Oxygeno Facial',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 14000/-',
                    'discounted_price' => 'Rs. 14000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 14000/-',
            'discounted_price' => 'Rs. 14000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Oxygeneo is an innovative technology for skin exfoliation (removal of dead skin cells from the skin surface), improving skin oxygen levels and infusion of valuable products to enrich the skin.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 14000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '01 Hour'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The requirement of client'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'photo-facial'  => [
            'heading' => 'Photo Facial',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/photo-facial.png',
                    'alt' => 'Photo Facial',
                    'link' => 'photo-facial',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Photo Facial',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 14000/-',
                    'discounted_price' => 'Rs. 14000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 14000/-',
            'discounted_price' => 'Rs. 14000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Intense pulsed light (IPL) therapy, aka photo facial, is a way to improve the color and texture of your skin without surgery. It can undo some of the visible damage caused by sun exposure, called photo aging. You may notice it mostly on your face, neck, hands, or chest.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 14000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '01 Hour'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The requirement of client'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'hydra-facial'=>[
            'basic-simple-hydrafacial'=>'basicsimplehydrafacial',
            'oxygeno-facial'=>'oxygenofacial',
            'photo-facial'=>'photofacial',
        ],

        'carbon-peel-laser'  => [
            'heading' => 'Carbon Peel Laser',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/carbon-peel-laser.png',
                    'alt' => 'Carbon Peel Laser',
                    'link' => 'carbon-peel-laser',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Carbon Peel Laser',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 14000/-',
                    'discounted_price' => 'Rs. 14000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 14000/-',
            'discounted_price' => 'Rs. 14000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'A carbon peel is a revolutionary laser treatment that is completely painless with zero downtime. It is highly beneficial for people with oily skin, blackheads, enlarged pores, dull skin, and acne on the face or body.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 14000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '20 Minuets'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The Skin conditions'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '15 Days To 01 Month'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],

        'lip-filler'  => [
            'heading' => 'Lip Fillers',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/lip-filler.png',
                    'alt' => 'Lip Fillers',
                    'link' => 'lip-filler',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Lip Fillers',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 30000/-',
                    'discounted_price' => 'Rs. 30000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 30000/-',
            'discounted_price' => 'Rs. 30000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Fillers consist of synthetic hyaluronic acid (HA).  HA is a natural substance in human body. People opt for lip injections because they want to smooth out their lips and get more fullness. They want to look younger and sexier, so they want to erase a few lines and achieve some youthful plumpness.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 30000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '30 Minuets'
                    ],
                    '4' => [
                        'name' => 'Gap Between Each Session',
                        'value' => 'Lasts For 12 To 18 Months'
                    ],
                    '5' => [
                        'name' => 'Downtime',
                        'value' => '3-4 Days'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'laugh-nasolabial-lines'  => [
            'heading' => 'Laugh Lines / Nasolabial Lines',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/laugh-nasolabial-lines.png',
                    'alt' => 'Laugh Lines / Nasolabial Lines',
                    'link' => 'laugh-nasolabial-lines',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Laugh Lines / Nasolabial Lines',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 35000/-',
                    'discounted_price' => 'Rs. 35000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 35000/-',
            'discounted_price' => 'Rs. 35000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'The nasolabial folds, commonly known as smile lines or laugh lines, are facial features. They are the two skin folds that run from each side of the nose to the corners of the mouth.</br>
                Treatment option for wrinkles around mouth and nose is laugh line fillers which has hyaluronic acid as their main ingredient, which lifts and plumps skin by attracting natural moisture and collagen. it improves the appearance of facial lines and volume loss caused by age or certain medical conditions.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 35000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '45 Minuets'
                    ],
                    '4' => [
                        'name' => 'Gap Between Each Session',
                        'value' => 'Lasts For 12 To 18 Months'
                    ],
                    '5' => [
                        'name' => 'Downtime',
                        'value' => '4 to 7 days'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'under-eye-filler'  => [
            'heading' => 'Under Eye Fillers',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/under-eye-filler.png',
                    'alt' => 'Under Eye Fillers',
                    'link' => 'under-eye-filler',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Under Eye Fillers',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 30000/-',
                    'discounted_price' => 'Rs. 30000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 30000/-',
            'discounted_price' => 'Rs. 30000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Eye fillers are used to lighten the tear trough, or under-eye area. They make that area look plumper and brighter. Reducing under-eye shadows can make you look well rested. Filler is a great option for people who lack volume under the eyes.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 30000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '45 Minuets'
                    ],
                    '4' => [
                        'name' => 'Gap Between Each Session',
                        'value' => 'Lasts For 12 To 18 Months'
                    ],
                    '5' => [
                        'name' => 'Downtime',
                        'value' => '4 to 7 days'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'fillers'=>[
            'lip-filler'=>'lipfiller',
            'laugh-nasolabial-lines'=>'laughnasolabiallines',
            'under-eye-filler'=>'undereyefiller ',
        ],

        'forhead-lines'  => [
            'heading' => 'Botox for Forehead Lines',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/forhead-lines.png',
                    'alt' => 'Forehead Lines',
                    'link' => 'forhead-lines',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Forehead Lines',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 25000/-',
                    'discounted_price' => 'Rs. 25000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 25000/-',
            'discounted_price' => 'Rs. 25000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Forehead wrinkles are caused by the action of the frontalis muscle on the forehead. This muscle contracts when we raise our eyebrows. The raising of the frontalis muscle pulls the skin of the forehead up and causes forehead wrinkles which appear as lines across our forehead.</br>
                                Botox ( Botulinum Toxin) is the best and safest Treatment option to treat forehead lines.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 25000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '15 Minuets'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'One Time Only (Effective For Six Months)'
                    ],
                    '5' => [
                        'name' => 'Downtime',
                        'value' => '24 Hours'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'crows-feet'  => [
            'heading' => 'Botox for Crows Feet ( Eye wrinkles )',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/crows-feet.png',
                    'alt' => 'Crow’s Feet',
                    'link' => 'crows-feet',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Crow’s Feet',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 25000/-',
                    'discounted_price' => 'Rs. 25000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 25000/-',
            'discounted_price' => 'Rs. 25000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Crows feet is the term given to those fine lines around the eye area. These tiny wrinkles might also be known as “smile lines” since they are the ones that form when we grine.</br>
                    Botox (Botulinum Toxin) is the best and safest Treatment option to treat crow’s feet.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 25000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '15 Minuets'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Injection One Time Only (Effective For Six Months)'
                    ],
                    '5' => [
                        'name' => 'Downtime',
                        'value' => '24 Hours'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'botox'=>[
            'forhead-lines'=>'forheadlines',
            'crows-feet'=>'crowsfeet',
        ],

        'thread-face-lift'  => [
            'heading' => 'Thread Face Lift ( Long Threads / Cog Threads )',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/thread-face-lift.png',
                    'alt' => 'Thread Face Lift ( Long Threads / Cog Threads )',
                    'link' => 'thread-face-lift',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Thread Face Lift ( Long Threads / Cog Threads )',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 30000/- Per Pair',
                    'discounted_price' => 'Rs. 30000/- Per Pair',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 90000/-',
            'discounted_price' => 'Rs. 90000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'A thread lift is a Non invasive cosmetic procedure that tightens the skin on the face. During a thread lift, an aesthetic physician uses threads to pull up portions of the skin to reduce the appearance of aging.</br>
                If a person looses skin on face, it may causes to develop jowls or drooping cheeks. A thread lift is a cosmetic procedure that can help tighten loose facial skin. During a thread lift, the cosmetic physician Inserts and slightly pulls the threads and the skin up, which ultimately lift and tighten the face. Along with lifting the skin and making it appear tighter, thread lift can also stimulate the body to direct large amounts of collagen to the treated areas. This can also impact the appearance of skin aging as collagen can help reduce or delay trusted source the skin-aging process.</br>
                Thread lifts are low risk and noninvasive and their recovery is often easier. There is virtually no risk of scarring. Due to the noninvasive nature of thread lifts, a person is at low risk of complications. A thread lift is alternative to a facelift. A person can drive home after the procedure and does not require care afterward. A person will also not require strong pain medication after a thread lift. This means that they can more easily return to their normal routine.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Normally Three pairs required per person',
                        'value' => ''
                    ],
                    '3' => [
                        'name' => 'Cost Per Two threads',
                        'value' => 'Rs. 30000/-'
                    ],
                    '4' => [
                        'name' => 'Procedure Time Required',
                        'value' => '01 Hour'
                    ],
                    '5' => [
                        'name' => 'Total Session Required',
                        'value' => 'One time Only'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => '02 to 03 Days'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'high-intensity-focused-ultrasound'  => [
            'heading' => 'High-intensity focused ultrasound (HIFU)',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/high-intensity-focused-ultrasound.png',
                    'alt' => 'High-intensity focused ultrasound (HIFU)',
                    'link' => 'high-intensity-focused-ultrasound',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'High-intensity focused ultrasound (HIFU)',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 30000/-',
                    'discounted_price' => 'Rs. 30000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 30000/-',
            'discounted_price' => 'Rs. 30000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'High-intensity focused ultrasound (HIFU) is a relatively new cosmetic treatment for skin tightening that some consider a noninvasive and painless replacement for face lifts. It uses ultrasound energy to encourage the production of collagen, which results in firmer skin.</br>
                We highly recommend pairing the treatment with regular exercise and a healthy diet for best results. </br>
                HIFU will need to be repeated 6 to 8 weeks apart after the initial treatment. The target area and size of the unwanted fat pockets will help determine how many treatments you will need. </br>
                HIFU is suitable for people aged approximately 25+ years with mild to moderate skin laxity or sagging. The device uses the modern technology of high intensity focused ultrasonic waves (HIFU). It produces excellent results in face, chin and neck slimming and removing buccal face fat tissues.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Session',
                        'value' => 'Rs. 30000/-'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '45 Minuets'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The requirement of client'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '6 to 8 weeks'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'non-surgical-face-lift'=>[
            'thread-face-lift'=>'threadfacelift',
            'high-intensity-focused-ultrasound'=>'highintensityfocusedultrasound',
        ],

        'non-surgical-breast-lift'  => [
            'heading' => 'Non Surgical Breast Lift (Thread Lift)',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/non-surgical-breast-lift.png',
                    'alt' => 'Non Surgical Breast Lift (Thread Lift)',
                    'link' => 'non-surgical-breast-lift',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Non Surgical Breast Lift (Thread Lift)',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 120000/-',
                    'discounted_price' => 'Rs. 120000/-',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 120000/-',
            'discounted_price' => 'Rs. 120000/-',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'During the non surgical breast lift, the cosmetic physicians insert Long/Cog threads into the skin through tiny incisions. These threads attach to the skin tissue and are then pulled to lift and smooth the skin. Patients remain fully conscious during the procedure.</br>
                The threads are then secured together and pulled upward toward collarbone to lift the breasts. The procedure is noninvasive breast lift, with results lasting up to 2 years.</br>
                Similar to a cheek or jowl lift, a thread lift for the breast involves placing threads into the breast to lift up fallen tissue. It gives fantastic results for fallen breasts. Threads can also help tighten loose skin on the abdomen.  We can also use threads to sculpt the jaw line, reduce the appearance of lines, improve wrinkles and lift and tighten the face and neck.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'It is done with local Anesthesia',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Cost Per Two threads',
                        'value' => 'Rs. 30000/-'
                    ],
                    '3' => [
                        'name' => 'Normally Four pairs of threads required per person',
                        'value' => ''
                    ],
                    '4' => [
                        'name' => 'Procedure Time Required',
                        'value' => '01 Hour'
                    ],
                    '5' => [
                        'name' => 'Total Session Required',
                        'value' => 'One time Only'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => '02 to 03 Days'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'fat-reduction-fat-freezing-cryolipolysis'  => [
            'heading' => 'Fat Reduction / Fat Freezing / Cryolipolysis (Clatuu Laser)',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/fat-reduction-fat-freezing-cryolipolysis.png',
                    'alt' => 'Fat Reduction / Fat Freezing / Cryolipolysis',
                    'link' => 'fat-reduction-fat-freezing-cryolipolysis',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Fat Reduction / Fat Freezing / Cryolipolysis',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Not given',
                    'discounted_price' => '',
                ],
            ],
            'is_discount'=>false,
            'price' => '',
            'discounted_price' => '',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Clatuu Alpha is a non-invasive treatment that freezes and destroys fat cells without causing damage to the skin and other tissues. Clatuu is a treatment technology that offers a more modern and effective fat freezing technique.</br>
                Clatuu is an alternative method to traditional liposuction. It is non-surgical. The process of cryogenic lipolysis involves freezing fat cells. The temperature that is used in Clatuu freezes the fat to a degree that allows for the cells to be killed without harming the skin surrounding it.</br>
                CLATUU is the latest non-surgical body-sculpting device offering suitable patients a good alternative to surgical fat reduction. CLATUU permanently destroys fat through a process known as cryolipolysis, or fat freezing.</br>
                Whilst some people see instant results, fat freezing is not an instant, one stop treatment. Unlike liposuction, your body has to naturally dispose of its own fat cells, and this can take up to 12 weeks after treatment, so you will see more changes as time goes on.</br></br>
                Areas treatable include: double chins, inner and outer thighs, knees, love handles, tummy, flanks, arms, buttocks and back.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => ''
                    ],
                    '2' => [
                        'name' => 'Total Cost Per Session',
                        'value' => 'Depends On The Part Of The Body'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => 'Time Depends On The Part Of The Body'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends On The requirement of client'
                    ],
                    '5' => [
                        'name' => 'Gap Between Each Session',
                        'value' => '6 to 8 weeks'
                    ],
                    '6' => [
                        'name' => 'Downtime',
                        'value' => 'No'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'mole-removal'  => [
            'heading' => 'Mole Removal',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/mole-removal.png',
                    'alt' => 'Mole Removal',
                    'link' => 'mole-removal',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Mole Removal',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Rs. 6000/ Per Mole',
                    'discounted_price' => 'Rs. 6000/ Per Mole',
                ],
            ],
            'is_discount'=>false,
            'price' => 'Rs. 6000/ Per Mole',
            'discounted_price' => 'Rs. 6000/ Per Mole',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Mole removal by laser can address your cosmetic concerns in one simple procedure which is totally non invasive. Results are permanent and our patients find that they are able to enjoy life to the fullest after the removal of a noticeable mole.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => '',
                    ],
                    '2' => [
                        'name' => 'Total Cost Per Session',
                        'value' => 'Rs. 6000/- Per Mole'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => '10 Min Per Mole'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'One Session Only'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
        'tattoo-removal'  => [
            'heading' => 'Tattoo Removal by Laser',
            'title' => 'title',
            'author' => 'author',
            'keywords' => 'keywords',
            'description' => 'description',
            'heading_class' => 'main_heading',
            'images' => [
                '0' => [
                    'src' => '/assets/images/tattoo-removal.png',
                    'alt' => 'Tattoo Removal by Laser',
                    'link' => 'tattoo-removal',
                    'linkclass' => 'text-decoration-none link-dark',
                    'heading' => 'Tattoo Removal by Laser',
                    'class' => 'card-img-top',
                    'is_discount'=>false,
                    'price' => 'Depends on the size of tattoo',
                    'discounted_price' => '',
                ],
            ],
            'is_discount'=>false,
            'price' => '',
            'discounted_price' => '',
            'about' => [
                'heading_class' => 'about_heading',
                'heading' => 'About',
                'description' => 'Lasers can remove tattoos completely. In fact, lasers are the safest, most effective tool to remove unwanted tattoos with. However, you may need to receive more than one session before the tattoo is removed completely.'
            ],
            'procedure' => [
                'heading_class' => 'procedure_heading',
                'heading' => 'Procedure',
                'details' => [
                    '1' => [
                        'name' => 'Total Pain Free',
                        'value' => '',
                    ],
                    '2' => [
                        'name' => 'Total Cost Per Session',
                        'value' => 'Depends on the size of tattoo'
                    ],
                    '3' => [
                        'name' => 'Procedure Time Required',
                        'value' => 'Depends on the size of tattoo'
                    ],
                    '4' => [
                        'name' => 'Total Session Required',
                        'value' => 'Depends on the depth of tattoo'
                    ],
                ],
            ],
            'buttons' => [
                "0"=>[
                'text' => 'Make Appointment',
                'href' => 'appointment',
                'class' => 'btn btn-orange fw-bold w-100 fs-5 py-2',
                'active' => true,
                ]
            ],
        ],
    ],

    'services' => [
        '1' => 'Candela Laser Hair Removal for Face & Neck',
        '2' => 'Q-Switch Laser for Melasma/Pigmentation',
        '3' => 'CO2 Fractional Laser for Acne Scars',
        '4' => 'Oxygeneo Facial + Photo Facial',
        '5' => 'Glutathione with Vit C,D & E Cocktail',
        '6' => 'Mesotherapy + PRP for Face with Microneedling',
        '7' => 'PRGF for Scalp Hair Regrowth',
        '8' => 'PRP for Scalp Hair Regrowth',
        '9' => 'Laugh Lines / Nasolabial Lines Filler',
        '10' => 'Thread Lift / 2 Threads (Large Threads)',
        '11' => 'HIFU for Non-Surgical Face Lift',
        '12' => 'Microblading for Eye Brows',
        '13' => 'Carbon Peel for Skin Glow',
        '14' => 'Skin Tag / Mole Removal',
        '15' => 'Lip Fillers',
        '16' => 'Under Eye Filler',
        '17' => 'Hydra Facial',
        '18' => 'Full Face and Neck Hair Removal',
        '19' => 'Half Face Hair Removal',
        '20' => 'Chin Hair Removal',
        '21' => 'Full Arms Hair Removal',
        '22' => 'Half Arms Hair Removal',
        '23' => 'Full Legs Hair Removal',
        '24' => 'Half Legs Hair Removal',
        '25' => 'Under Legs Hair Removal',
        '26' => 'Under Arms Hair Removal',
        '27' => 'Full Body Hair Removal',
        '28' => 'Male Beard Line (Top) Hair Removal',
        '29' => 'Male Beard Line & Neck Hair Removal',
        '30' => 'Acne Scars Removal',
        '31' => 'Mole Removal',
        '32' => 'Skin Tag Removal',
        '33' => 'Wart Removal',
        '34' => 'Full Face and Neck Q-Switch Laser',
        '35' => 'Half Face Q-Switch Laser',
        '36' => 'Tattoo Removal',
        '37' => 'Gold Tonning',
        '38' => 'Forhead Lines',
        '39' => 'Crows Feet',
        '40' => 'Forhead lines+Crows Feet',
        '41' => 'Meso Botox',
    ],
    'social' => [
        'instagram' => ['is_active' =>true,'link' => 'https://instagram.com/drmubashirdaha', 'icon' => 'bi bi-instagram', 'class' => 'bg-dark','alt' => 'Instagram'],
        'facebook' => ['is_active' =>true,'link' => 'https://www.facebook.com/drmubashirdaha/', 'icon' => 'bi bi-facebook', 'class' => 'bg-dark','alt' => 'Facebook'],
        'tiktok' => ['is_active' =>true,'link' => 'https://vt.tiktok.com/ZSRdmoVdk/', 'icon' => 'bi bi-tiktok', 'class' => 'bg-dark','alt' => 'Tiktok'],
        'youtube' => ['is_active' =>true,'link' => 'https://youtu.be/wSHp7SymJ-M', 'icon' => 'bi bi-youtube', 'class' => 'bg-dark','alt' => 'Youtube'],
        'snapchat' => ['is_active' =>true,'link' => 'https://www.snapchat.com/add/drmubashirdaha?share_id=ANubH7TP2gU&locale=en-PK',  'icon' => 'bi bi-snapchat', 'class' => 'bg-dark','alt' => 'Snapchat'],

    ]
];
