<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pre-Populate configuration for each new organisation setup
    |--------------------------------------------------------------------------
    */

    'categories' => [
        [
            'name' => 'Tattoo',
            'services' => [
                [
                    'name' => 'Tattoo Consultation',
                    'color'=> '#000000',
                    'duration' => 60,
                    'description' => null,
                ],
                [
                    'name' => 'Lettering Tattoo',
                    'duration' => 60,
                    'color' => '#2185d0',
                    'description' => "Gothic, intricate, delicate fonts are popular with tattoo designs but there's no limit to which font you can chose. Take lots of examples so your artist can create a custom lettering rather than tracing something you've printed off the internet. You want to make sure you're getting something no one else in the world has on their skin. And if it's a language other than your mother tongue: double, triple, quadruple check your translation. And then again.",
                ],
                [
                    'name' => 'Realism Tattoo',
                    'duration' => 60,
                    'color' => '#faff00',
                    'description' => "Often uses photographs to replicate something in the real world down to every single detail. Popular images: animals, portraits, flowers, nature, pets. To exactly replicate a photo onto someone\'s skin is a fine art. Do a lot of research into your artist and pick the best.",
                ],
                [
                    'name' => 'Dotwork Tattoo',
                    'duration' => 60,
                    'color' => '#a333c8',
                    'description' => 'Beautiful designs that use lots of tiny dots in areas where you might normally shade or colour. Often the designs are geometric shapes but dot-work can be used in almost anything (animals, flowers, etc).',
                ],
                [
                    'name' => 'New School Tattoo',
                    'duration' => 60,
                    'color' => '#1cecf2',
                    'description' => 'Cartoon designs often influenced by graffiti, caricature and hip-hop styles. Bubble-like designs, bright colours, exaggerated dimensions. Quite popular with portraits, Disney characters, animals, pets, celebrities, film and TV characters.',
                ],
                [
                    'name' => 'Neo-Traditional Tattoo',
                    'duration' => 60,
                    'color' => '#fbea08',
                    'description' => 'Like Traditional tattoos, Neo-Traditional use bold outlines and lots of space to create tattos that stand the test of time. Compared to Traditional tattoos, they are more detailed, more experimental with colour and blending, more dimensional and definitely brighter – yet still adhering to the symbology and technique Traditional. Neo-Traditional might also merge different styles together, be elaborate and experimental and try to be as unique and custom as possible.',
                ],
                [
                    'name' => 'Colour Tattoo',
                    'duration' => 60,
                    'color' => '#21ba45',
                    'description' => 'A design that allows an exciting, bold or innovative use of different colours and colour schemes. The techniques for creating a black/grey and a colour tattoo, are very different, and so many artists may specialise in only one or the other.',
                ],
                [
                    'name' => 'Black and Grey Tattoo',
                    'duration' => 60,
                    'color' => '#db2828',
                    'description' => 'Realistic or non-realistic tattoos that use only black ink and watered down black ink (to create grey). Lots of shading, often fine lines and sometimes a dash of white for highlights.',
                ],
                [
                    'name' => 'Abstract / Surreal Tattoo',
                    'duration' => 60,
                    'color' => '#91abed',
                    'description' => 'Non-realistic tattoos that might use streaks of colours, unusual shapes and surreal imagery to represent something in an abstract, artistic, unique way.',
                ],
                [
                    'name' => 'Old School / Traditional Tattoo',
                    'duration' => 60,
                    'color' => '#e03997',
                    'description' => "One of the most popular styles of tattooing today and where tattooing began in the modern sense of the word120of space between lines (less detail)… these are tattoos that will last. Because they are typically less intricate traditional tattoos hold up over time extremely well. Popular symbols are roses, hearts, daggers, nautical themes, eagles, skulls, diamonds, women's heads and ships.",
                ],
                [
                    'name' => 'Watercolour Tattoo',
                    'duration' => 60,
                    'color' => '#a333c8',
                    'description' => 'Watercolour tattoos mimic splashes, streaks or spots of colour similar to splashing paint on a canvas. Often the tattoo might be realistic, or have lots of black lines, and the watercolour effect is added in the background or around the tattoo. Watercolour tattoos are often very colourful and are coupled with themes of nature, animals and flowers.',
                ],
                [
                    'name' => 'Asian / Japanese Tattoo',
                    'duration' => 60,
                    'color' => '#f2711c',
                    'description' => 'Very detailed, intricate designs that more often than not are designed to cover a whole limb, back or body 120positioning - a very experienced Japanese style tattooer will have a reason behind every tiny decision made down to the movement of waves and the way an animal might face.',
                ],
                [
                    'name' => 'Tribal Tattoo',
                    'duration' => 60,
                    'color' => '#fbbd08',
                    'description' => "One of the oldest styles of traditional tattooing from ancient times. Usually black in colour, often using symmetry and geometrical designs, they're a real test of an artists line work and steady hand.",
                ],

            ],
        ],
        [
            'name' => 'Piercing',
            'services' => [
                [
                    'name' => 'Control/Change',
                    'color' => '#646c66',
                    'duration'=>15,
                ],
                [
                    'name' => 'Cut (from 5mm)',
                    'color' => '#96a699',
                    'duration'=>60,
                ],
                [
                    'name' => 'Ear Lobe',
                    'color' => '#faca00',
                    'duration'=>30,
                ],
                [
                    'name' => 'Snug',
                    'color' => '#929fd5',
                    'duration'=>30,
                ],
                [
                    'name' => 'Rook',
                    'color' => '#2da3b8',
                    'duration'=>30,
                ],
                [
                    'name' => 'Industrial',
                    'color' => '#fcf30e',
                    'duration'=>60,
                ],
                [
                    'name' => 'Daith',
                    'color' => '#90536b',
                    'duration'=>30,
                ],
                [
                    'name' => 'Orbital',
                    'color' => '#b1afb6',
                    'duration'=>40,
                ],
                [
                    'name' => 'Helix',
                    'color' => '#0a87ee',
                    'duration'=>30,
                ],
                [
                    'name' => 'Conch',
                    'color' => '#af662b',
                    'duration'=>30,
                ],
                [
                    'name' => 'Tragus',
                    'color' => '#b9d40c',
                    'duration'=>30,
                ],
                [
                    'name' => 'Cheek',
                    'color' => '#ceebe1',
                    'duration'=>30,
                ],
                [
                    'name' => 'Septum',
                    'color' => '#822510',
                    'duration'=>30,
                ],
                [
                    'name' => 'Nostril',
                    'color' => '#ed7373',
                    'duration'=>30,
                ],
                [
                    'name' => 'Bridge',
                    'color' => '#c6a040',
                    'duration'=>60,
                ],
                [
                    'name' => 'Eye',
                    'color' => '#6c9cc8',
                    'duration'=>30,
                ],
                [
                    'name' => 'Labret',
                    'color' => '#dac69e',
                    'duration'=>30,
                ],
                [
                    'name' => 'Male genitals',
                    'color' => '#00ff8c',
                    'duration'=>60,
                ],
                [
                    'name' => 'Female genitals',
                    'color' => '#a425e8',
                    'duration'=>60,
                ],
                [
                    'name' => 'Smiley',
                    'color' => '#f98702',
                    'duration'=>30,
                ],
                [
                    'name' => 'Venom',
                    'color' => '#d5c7f0',
                    'duration'=>60,
                ],
                [
                    'name' => 'Tongue',
                    'color' => '#2db84b',
                    'duration'=>30,
                ],
                [
                    'name' => 'Navel',
                    'color' => '#f45fb3',
                    'duration'=>30,
                ],
                [
                    'name' => 'Surface',
                    'color' => '#21d0c5',
                    'duration'=>60,
                ],
                [
                    'name' => 'Nipple',
                    'color' => '#d9200e',
                    'duration'=>30,
                ],
                [
                    'name' => 'Microdermal',
                    'color' => '#1e20e7',
                    'duration'=>30,
                ],
            ],
        ],
    ],
    'consentForm' => [
        'tattoo' => [
            'name' => 'Default tattoo consent form',
            'logo' => '',
            'title' => 'CONSENT TO THE TATTOO PROCEDURE',
            'subtitle' => 'PLEASE READ AND BE SURE YOU UNDERSTAND THE IMPLICATIONS OF SIGNING',
            'opening_text' => 'By signing this agreement I acknowledge that I have been given the full opportunity to ask any questions I may have about getting a tattoo and that all of my questions have been answered to my complete satisfaction. I specifically acknowledge that I have been advised of the facts and matters below and agree as follows:',
            'statements' => [
                'If I have a condition that could affect healing from this tattoo, I will notify my tattoo artist. I am not pregnant or breastfeeding. I am not under the influence of alcohol or drugs.',
                'I have no medical or skin conditions such as, but not limited to: acne, scarring (keloid) eczema, psoriasis, freckles, moles or sunburn in the area to be tattooed that could interfere with said tattoo. If I have any type of infection or rash ANYWHERE on my body, I will notify my tattoo artist.',
                'I acknowledge that it is not reasonably possible for representatives and associates of this tattoo shop to determine whether I may have an allergic reaction to the pigments or procedures used in my tattoo, and I agree to accept the risk of such a reaction occurring.',
                'I recognize that it is always possible to get an infection as a result of getting a tattoo, particularly in the event that I do not take care of my tattoo. I have received aftercare instructions and agree to follow them while my tattoo is healing. I agree that if any touch-up work becomes necessary due to my negligence, it will be done at my expense.',
                'I understand that there may be variations in color and design between the tattoo I choose and the one created on my body. I understand that if my skin color is dark, the colors will not appear as bright as on light skin.',
                'I understand that having skin treatments, laser hair removal, plastic surgery, or other procedures that alter the skin may cause adverse changes to my tattoo.',
                'I recognize that a tattoo is a permanent change to my appearance and have not been given any guarantees such as the ability to later change or remove my tattoo. To my knowledge, I do not have a physical, mental or health impairment or disability that would impact my well-being as a direct or indirect consequence of my decision to have a tattoo.',
                'I sincerely expressed to my tattoo artist that having a tattoo is my spontaneous choice. I consent to the tattoo application and any actions or conduct of tattoo shop representatives and employees reasonably necessary to perform the tattooing procedure.',
            ],
            'closing_text' => 'If any provision, section, subsection, clause or phrase of this Agreement is found to be unenforceable or invalid, that portion will be severed from this Agreement. the remainder of this agreement will then be construed as if the unenforceable portion had never been contained herein. I certify that I am an adult (and have provided valid proof of my age) and that I have the authority to sign this agreement or, if not, my parent or legal guardian must sign on my behalf, and that my parent or guardian legal has fully understood and is in agreement with this contract.',
            'sign_title' => 'I HAVE READ AND UNDERSTAND THIS AGREEMENT AND AGREE TO BE BOUND BY IT',
            'needs_signature' =>true,
            'notes' =>'',
            'is_active' =>true,
            'use_custom_consent' =>false,
            'text' =>'',
            'infant_consent' =>false,
        ],
        'piercing' => [
            'name' => 'Default piercing consent form',
            'logo' => '',
            'title' => 'CONSENT TO THE TATTOO PROCEDURE',
            'subtitle' => 'PLEASE READ AND BE SURE YOU UNDERSTAND THE IMPLICATIONS OF SIGNING',
            'opening_text' => 'By signing this agreement I acknowledge that I have been given the full opportunity to ask any questions I may have about getting a tattoo and that all of my questions have been answered to my complete satisfaction. I specifically acknowledge that I have been advised of the facts and matters below and agree as follows:',
            'statements' => [
                'If I have a condition that could affect healing from this tattoo, I will notify my tattoo artist. I am not pregnant or breastfeeding. I am not under the influence of alcohol or drugs.',
                'I have no medical or skin conditions such as, but not limited to: acne, scarring (keloid) eczema, psoriasis, freckles, moles or sunburn in the area to be tattooed that could interfere with said tattoo. If I have any type of infection or rash ANYWHERE on my body, I will notify my tattoo artist.',
                'I acknowledge that it is not reasonably possible for representatives and associates of this tattoo shop to determine whether I may have an allergic reaction to the pigments or procedures used in my tattoo, and I agree to accept the risk of such a reaction occurring.',
                'I recognize that it is always possible to get an infection as a result of getting a tattoo, particularly in the event that I do not take care of my tattoo. I have received aftercare instructions and agree to follow them while my tattoo is healing. I agree that if any touch-up work becomes necessary due to my negligence, it will be done at my expense.',
                'I understand that there may be variations in color and design between the tattoo I choose and the one created on my body. I understand that if my skin color is dark, the colors will not appear as bright as on light skin.',
                'I understand that having skin treatments, laser hair removal, plastic surgery, or other procedures that alter the skin may cause adverse changes to my tattoo.',
                'I recognize that a tattoo is a permanent change to my appearance and have not been given any guarantees such as the ability to later change or remove my tattoo. To my knowledge, I do not have a physical, mental or health impairment or disability that would impact my well-being as a direct or indirect consequence of my decision to have a tattoo.',
                'I sincerely expressed to my tattoo artist that having a tattoo is my spontaneous choice. I consent to the tattoo application and any actions or conduct of tattoo shop representatives and employees reasonably necessary to perform the tattooing procedure.',
            ],
            'closing_text' => 'If any provision, section, subsection, clause or phrase of this Agreement is found to be unenforceable or invalid, that portion will be severed from this Agreement. the remainder of this agreement will then be construed as if the unenforceable portion had never been contained herein. I certify that I am an adult (and have provided valid proof of my age) and that I have the authority to sign this agreement or, if not, my parent or legal guardian must sign on my behalf, and that my parent or guardian legal has fully understood and is in agreement with this contract.',
            'sign_title' => 'I HAVE READ AND UNDERSTAND THIS AGREEMENT AND AGREE TO BE BOUND BY IT',
            'needs_signature' =>true,
            'notes' =>'',
            'is_active' =>true,
            'use_custom_consent' =>false,
            'text' =>'',
            'infant_consent' =>false,
        ],
    ],
    'notificationsContent' => [
        [
            'slug' => 'customer_appointment_created',
            'channel' => 'email',
            'entity' => 'customer',
            'name' => 'Customer Appointment Created',
            'subject' => 'Appointment created',
            'message' => '<p>Hello <b>{customer}</b>,</p>
            <p>Your appointment with {staff} / {company} was created.</p>
            <p><b>When</b>: {date}</p>
            <p><b>Service</b>: {service}</p>
            <p><b>With</b>: {staff}</p>',
        ],
        [
            'slug' => 'customer_appointment_rescheduled',
            'channel' => 'email',
            'entity' => 'customer',
            'name' => 'Customer Appointment Rescheduled',
            'subject' => 'Appointment rescheduled',
            'message' => '<p>Hello <b>{customer}</b>,</p>
            <p>Your appointment with {staff} / {company} was rescheduled.</p>
            <p><b>From:</b> {start_time}</p>
            <p><b>To:</b> {end_time}</p>
            <p><b>Service:</b> {service}</p>
            <p><b>With:</b> {staff}</p>',
        ],
        [
            'slug' => 'customer_appointment_canceled',
            'channel' => 'email',
            'entity' => 'customer',
            'name' => 'Customer Appointment Canceled',
            'subject' => 'Appointment canceled',
            'message' => '<p>Hello <b>{customer}</b>,</p>
            <p>Your appointment with {staff} / {company} was canceled.</p>
            <p><b>When</b>: {date}</p>
            <p><b>Service</b>: {service}</p>
            <p><b>With</b>: {staff}</p>',
        ],
        [
            'slug' => 'customer_appointment_reminder',
            'channel' => 'email',
            'entity' => 'customer',
            'name' => 'Customer Appointment Reminder',
            'subject' => 'Appointment reminder',
            'message' => '<p>Hello <b>{customer}</b>,</p>
            <p>Don\'t forget your scheduled appointment.</p>
            <p><b>When:</b> {date}</p>
            <p><b>Duration:</b> {duration}</p>
            <p><b>Service:</b> {service}</p>
            <p><b>Staff:</b> {staff}</p>',
        ],
        [
            'slug' => 'customer_post_appointment',
            'channel' => 'email',
            'entity' => 'customer',
            'name' => 'Customer Post appointment Email',
            'subject' => ' Post-Tattoo and Post-Piercing Care Instructions',
            'message' => '<p>Hello <b>{customer}</b>,</p>
            <p>Thank you for choosing us for your tattoo and piercing needs. </p>
            <p>Proper aftercare is crucial to ensure optimal healing and prevent complications.</p>
            <p>Please follow the instructions below for your new tattoo and piercing:</p>
            <h4>Post-Tattoo Care Instructions:</h4>
            <p>1.Keep It Clean:</p>
            <p>Gently wash the tattoo with lukewarm water and mild, fragrance-free soap.</p>
            <p>Pat dry with a clean towel; do not rub.</p>
            <p>2.Moisturize:</p>
            <p>Apply a thin layer of tattoo aftercare ointment or unscented lotion.</p>
            <p>Avoid over-applying; the skin needs to breathe.</p>
            <p>3.Avoid Sun Exposure:</p>
            <p>Keep the tattoo out of direct sunlight.</p>
            <p>Once healed, always apply sunscreen when exposed to the sun.</p>
            <p>4.Do Not Scratch or Pick:</p>
            <p>Let any scabs or peeling skin come off naturally.</p>
            <p>Avoid scratching, as it can cause scarring or infection.</p>
            <p>5.Wear Loose Clothing:</p>
            <p>Avoid tight clothing that may rub against the tattoo.</p>
            <p>6.Avoid Submersion:</p>
            <p>Do not submerge the tattoo in water (baths, pools, oceans) until fully healed.</p>
            <h4>Post-Piercing Care Instructions:</h4>
            <p>1.Clean Regularly:</p>
            <p>Clean the piercing twice daily with a saline solution or a piercing aftercare solution.</p>
            <p>Avoid using alcohol or hydrogen peroxide.</p>
            <p>2.Avoid Touching:</p>
            <p>Do not touch the piercing with unwashed hands.</p>
            <p>Avoid twisting or turning the jewelry unnecessarily.</p>
            <p>3.Keep Dry:</p>
            <p>Dry the area gently with a clean, disposable tissue or paper towel after cleaning.</p>
            <p>Avoid soaking the piercing in water (e.g., swimming) until it has healed.</p>
            <p>4.Watch for Irritation:</p>
            <p>Avoid sleeping on the piercing or wearing tight clothing/jewelry that could irritate it.</p>
            <p>5.Avoid Makeup and Lotions:</p>
            <p>Keep makeup, lotions, and hair products away from the piercing area.</p>
            <p>6.Be Patient:</p>
            <p>Healing times vary depending on the location of the piercing. Follow the specific healing time instructions provided by your piercer.</p>
            <p>If you have any questions or concerns during the healing process, please do not hesitate to contact us.</p>',
        ],
        [
            'slug' => 'staff_appointment_created',
            'channel' => 'email',
            'entity' => 'staff',
            'name' => 'Staff Appointment Created',
            'subject' => 'New appointment created',
            'message' => '<p>Hello <b>{staff}</b>,</p>
            <p>You have set up an appointment.</p>
            <p><b>When:</b> {date}</p>
            <p><b>Duration:</b> {duration}</p>
            <p><b>Service:</b> {service}</p>
            <p><b>Client:</b> {customer}</p>',
        ],
        [
            'slug' => 'staff_appointment_rescheduled',
            'channel' => 'email',
            'entity' => 'staff',
            'name' => 'Staff Appointment Rescheduled',
            'subject' => 'Appointment rescheduled',
            'message' => '<p>Hello <b>{staff}</b>,</p>
            <p>You have a rescheduled appointment.</p>
            <p><b>From:</b> {start_time}</p>
            <p><b>To </b>{end_time}</p>
            <p><b>Duration:</b> {duration}</p>
            <p><b>Service:</b> {service}</p>
            <p><b>Client:</b> {customer}</p>',
        ],
        [
            'slug' => 'staff_appointment_canceled',
            'channel' => 'email',
            'entity' => 'staff',
            'name' => 'Staff Appointment Canceled',
            'subject' => 'Appointment_canceled',
            'message' => '<p>Hello <b>{staff}</b>,</p>
            <p>Your appointment with {customer} was canceled.</p>
            <p><b>When:</b> {date}</p>
            <p><b>Duration:</b> {duration}</p>
            <p><b>Service:</b> {service}</p>
            <p><b>Client:</b> {customer}</p>',
        ],
        [
            'slug' => 'staff_appointment_reminder',
            'channel' => 'email',
            'entity' => 'staff',
            'name' => 'Staff Appointment Reminder',
            'subject' => 'Appointment reminder',
            'message' => '<p>Hello <b>{staff}</b>,</p>
            <p>Don\'t forget your scheduled appointment.</p>
            <p><b>When:</b> {date}</p>
            <p><b>Duration:</b> {duration}</p>
            <p><b>Service:</b> {service}</p>
            <p><b>Client:</b> {customer}</p>',
        ],
        [
            'slug' => 'staff_post_appointment',
            'channel' => 'email',
            'entity' => 'staff',
            'name' => 'Staff Post Appointment',
            'subject' => 'Post-Appointment',
            'message' => '<p>Hello <b>{staff}</b>,</p>
            <p>Don\'t forget to follow up with customer {customer}.</p>',
        ],
        [
            'slug' => 'customer_booking_vip',
            'channel' => 'email',
            'entity' => 'staff',
            'name' => 'Customer VIP Booking',
            'subject' => 'VIP Booking',
            'message' => '<p>Hello <b>{staff}</b>,</p>
            <p><b>{customer}</b> <b>👑{vip}</b>  has made an appointment with you. Please prioritize and review the details to ensure exceptional service.</p>
            <p><b>When:</b> {date}</p>
            <p><b>Duration:</b> {duration}</p>
            <p><b>Service:</b> {service}</p>
            <p><b>Client:</b> {customer}</p>',
        ],

        /// SMS
        [
            'slug' => 'customer_appointment_reminder',
            'channel' => 'sms',
            'entity' => 'customer',
            'name' => 'Appointment Reminder SMS',
            'message' => 'Hello {customer}! Don\'t forget your appointment on {date} with {staff}.',
        ],
        [
            'slug' => 'customer_post_appointment',
            'channel' => 'sms',
            'entity' => 'customer',
            'name' => 'Post appointment SMS',
            'message' => 'Hello {customer}! Please keep away from sun and moisturise the tattoo.',
        ],
        [
            'slug' => 'staff_appointment_reminder',
            'channel' => 'sms',
            'entity' => 'staff',
            'name' => 'Appointment Reminder SMS',
            'message' => 'Hello {staff}! Don\'t forget your appointment on {date} with {customer}.',
        ],
        [
            'slug' => 'staff_post_appointment',
            'channel' => 'sms',
            'entity' => 'staff',
            'name' => 'Post appointment SMS',
            'message' => 'Hello {staff}! Don\'t forget to follow up with {customer}.',
        ],
    ],
];
