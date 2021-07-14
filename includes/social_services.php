<?php

$mailster_social_services = array(
	'twitter'   => array(
		'name'   => 'Twitter',
		'url'    => 'https://twitter.com/intent/tweet?source=Mailster&text=%title&url=%url',
		'width'  => 845,
		'height' => 600,
		'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" height="244.187" width="300"><path d="M94.719 243.187c112.46 0 173.956-93.168 173.956-173.956 0-2.647-.054-5.28-.173-7.903A124.323 124.323 0 00299 29.668c-10.955 4.87-22.744 8.147-35.11 9.625 12.623-7.569 22.314-19.543 26.885-33.817a122.61 122.61 0 01-38.824 14.84C240.794 8.433 224.911 1 207.322 1c-33.763 0-61.144 27.38-61.144 61.132 0 4.798.537 9.465 1.586 13.94C96.948 73.517 51.89 49.188 21.738 12.194a60.978 60.978 0 00-8.278 30.73c0 21.212 10.793 39.938 27.207 50.893a60.69 60.69 0 01-27.69-7.647c-.01.257-.01.507-.01.781 0 29.61 21.076 54.332 49.052 59.934a61.22 61.22 0 01-16.122 2.152c-3.934 0-7.766-.387-11.49-1.103C42.19 172.227 64.76 189.904 91.52 190.4c-20.925 16.402-47.287 26.17-75.937 26.17-4.929 0-9.798-.28-14.584-.846 27.059 17.344 59.19 27.464 93.722 27.464" fill="#1da1f2"/></svg>',
	),
	'facebook'  => array(
		'name'   => 'Facebook',
		'url'    => 'https://www.facebook.com/sharer.php?display=popup&u=%url&t=%title',
		'height' => 600,
		'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="1365.12" height="1365.12" viewBox="0 0 14222 14222"><circle cx="7111" cy="7112" r="7111" fill="#1977f3"/><path d="M9879 9168l315-2056H8222V5778c0-562 275-1111 1159-1111h897V2917s-814-139-1592-139c-1624 0-2686 984-2686 2767v1567H4194v2056h1806v4969c362 57 733 86 1111 86s749-30 1111-86V9168z" fill="#fff"/></svg>',

	),
	'pinterest' => array(
		'name'   => 'Pinterest',
		'url'    => 'http://pinterest.com/pin/create/button/?url=%url&description=%title',
		'width'  => 750,
		'height' => 600,
		'icon'   => '<svg width="256" height="256" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid"><path d="M0 128.002c0 52.414 31.518 97.442 76.619 117.239-.36-8.938-.064-19.668 2.228-29.393 2.461-10.391 16.47-69.748 16.47-69.748s-4.089-8.173-4.089-20.252c0-18.969 10.994-33.136 24.686-33.136 11.643 0 17.268 8.745 17.268 19.217 0 11.704-7.465 29.211-11.304 45.426-3.207 13.578 6.808 24.653 20.203 24.653 24.252 0 40.586-31.149 40.586-68.055 0-28.054-18.895-49.052-53.262-49.052-38.828 0-63.017 28.956-63.017 61.3 0 11.152 3.288 19.016 8.438 25.106 2.368 2.797 2.697 3.922 1.84 7.134-.614 2.355-2.024 8.025-2.608 10.272-.852 3.242-3.479 4.401-6.409 3.204-17.884-7.301-26.213-26.886-26.213-48.902 0-36.361 30.666-79.961 91.482-79.961 48.87 0 81.035 35.364 81.035 73.325 0 50.213-27.916 87.726-69.066 87.726-13.819 0-26.818-7.47-31.271-15.955 0 0-7.431 29.492-9.005 35.187-2.714 9.869-8.026 19.733-12.883 27.421a127.897 127.897 0 0036.277 5.249c70.684 0 127.996-57.309 127.996-128.005C256.001 57.309 198.689 0 128.005 0 57.314 0 0 57.309 0 128.002z" fill="#CB1F27"/></svg>',

	),
	'buffer'    => array(
		'name'   => 'Buffer',
		'url'    => 'https://buffer.com/add?url=%url&text=%title',
		'width'  => 720,
		'height' => 600,
		'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"><path d="M.3 14.128L29.286.532c1.672-.78 3.232-.67 4.903.1L61.492 13.46c.67.334 1.45.557 1.45 1.337 0 1.003-.892 1.114-1.56 1.45l-26.2 12.257c-2.452 1.114-4.57 1.114-7.02 0L.3 15.577v-1.45zm0 34.658l5.126-2.563c2.117-1.114 4.123-.892 6.24.1l16.827 7.912c2.117 1.003 4.123 1.003 6.24 0l17.05-8.024c2.117-1.003 4.123-1.003 6.24 0 1.226.67 2.563 1.226 3.8 1.783.557.334 1.337.557 1.226 1.337 0 .67-.67 1.003-1.226 1.226L49.12 56.585 34.743 63.27c-1.894.892-3.8 1.003-5.683.1L2.094 50.792c-.557-.223-1.003-.67-1.56-1.003-.223-.223-.223-.557-.223-1.003zm0-17.273l3.9-1.894c3-1.783 5.795-1.45 8.915.1 5.238 2.675 10.587 4.903 15.824 7.466 2.006.892 3.8.892 5.683 0l16.827-7.912c2.34-1.114 4.57-1.226 6.9 0 1.226.67 2.675 1.226 3.9 1.894 1.114.67 1.003 1.337 0 2.006-.334.223-.67.334-1.003.446L34.412 46.1c-1.783.892-3.566.892-5.35 0L2.206 33.63c-.67-.334-1.226-.78-1.894-1.114v-1.003z"/></svg>',

	),
	'blogger'   => array(
		'name' => 'Blogger',
		'url'  => 'https://www.blogger.com/blog_this.pyra?t&u=%url&n=%title',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="191.488" height="191.151" viewBox="0 0 179.52 179.204"><path d="M20.512 178.499c-3.359-.884-6.258-2.184-8.931-4.006-2.257-1.538-5.556-4.717-6.81-6.563-1.533-2.255-3.294-6.117-4.012-8.795-.732-2.732-.743-3.82-.757-69.395-.013-65.245.002-66.68.72-69.483C3.259 10.34 11.117 2.797 21.252.547c2.913-.648 133.08-.76 136.222-.119 8.51 1.738 15.198 6.846 19.068 14.564 3.078 6.135 2.803-.617 2.943 72.23.09 46.35.007 65.81-.288 68.233-1.386 11.345-9.211 20.143-20.47 23.019-2.88.735-3.883.746-69.276.726-63.227-.02-66.474-.052-68.939-.701z" fill="#f06a35"/><path d="M-82.995 87.838v-171.9h1020v343.8h-1020v-171.9z" fill="none"/><path d="M115.162 144.835c8.064-1.1 14.384-4.333 20.313-10.39 4.289-4.382 6.974-9.125 8.728-15.42.73-2.614.79-3.887.924-19.24.1-11.589.017-17.016-.285-18.386-.437-1.986-1.677-3.83-3.092-4.599-.435-.237-3.224-.538-6.198-.67-4.982-.221-5.54-.318-7.113-1.24-2.494-1.462-3.181-3.04-3.188-7.327-.013-8.19-3.421-15.792-10.155-22.654-4.797-4.889-10.149-8.198-16.257-10.052-1.462-.444-4.736-.595-15.702-.725-17.207-.203-21.026.15-26.884 2.483-10.8 4.302-18.56 13.368-21.39 24.99-.532 2.183-.635 5.682-.76 25.779-.158 25.177.015 28.874 1.589 33.864 1.3 4.122 2.61 6.648 5.313 10.234 5.146 6.83 12.86 11.763 20.572 13.156 3.67.663 48.948.83 53.585.197z" fill="#fff"/><path d="M67.575 75.717c-4.123-1.136-5.663-7.051-2.633-10.11 1.937-1.956 2.472-2.03 14.595-2.03 10.883 0 11.25.023 12.848.83 2.31 1.168 3.314 2.813 3.314 5.433 0 2.367-.943 4.025-3.046 5.357-1.129.716-1.804.76-12.467.823-6.584.039-11.83-.087-12.611-.303zM67.058 115.526c-1.77-.771-3.417-2.913-3.702-4.813-.272-1.809.638-4.296 2.032-5.558 1.757-1.59 2.528-1.643 24.134-1.66 22.227-.017 22.111-.027 24.219 1.941 2.976 2.78 2.349 7.728-1.239 9.76l-3.686.6-19.213.224c-16.883.198-21.666-.111-22.545-.494z" fill="#f06a35"/></svg>',

	),
	'sharethis' => array(
		'name'   => 'ShareThis',
		'url'    => 'https://www.sharethis.com/share?url=%url&title=%title',
		'height' => 720,
		'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M355.9 500c0 3.4-.8 6.5-1 9.9L706 685.4c30.1-25.5 68.5-41.3 111-41.3 95.6-.1 173 77.4 173 172.9 0 95.6-77.4 173-172.9 173-95.6 0-172.9-77.4-172.9-173 0-3.4.8-6.5 1-9.9L294 631.6c-30.2 25.4-68.5 41.3-111 41.3-95.6-.1-173-77.4-173-172.9 0-95.6 77.4-173 172.9-173 42.5 0 80.9 16 111 41.3L645 192.8c-.2-3.4-1-6.5-1-10C644.1 87.4 721.5 10 817.1 10 912.6 10 990 87.4 990 182.8c0 95.6-77.4 173-172.9 173-42.6 0-81-16-111-41.4L354.9 490c.2 3.4 1 6.6 1 10z" fill="#91D400"/></svg>',

	),
	'reddit'    => array(
		'name' => 'Reddit',
		'url'  => 'https://en.reddit.com/submit?url=%url&title=%title',
		'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" viewBox="0 0 3593.8 3600" xml:space="preserve"><style>.st0{fill:#ff3f18}</style><g id="XMLID_1_"><path class="st0" d="M1756.5 76.2C2742.4 54 3550.3 859 3524.2 1853.5c-23.8 906.6-767.9 1688.3-1748.8 1678.4-934.6-9.4-1703.3-766.5-1708-1728.5C73.1 842.8 837.4 97 1756.5 76.2zm1257.8 1679.4c91.6-67.9 147.2-155.8 136.1-274.5-10.8-116.3-69.7-202.8-176.3-249.9-115.3-50.9-225.6-36.5-324.5 44.3-11.3 9.2-18.2 9.6-29.8 2-183.4-120.9-385.4-188.6-603.4-210.7-24.4-2.5-48.8-5.1-76.8-8 47.3-147.4 93.5-291 140.1-436 11.2 1.8 20.2 2.8 29 4.7 111.3 23.9 222.4 48.6 333.9 71.3 23 4.7 34.3 12.7 40.8 36.9C2514.2 850 2628 922.9 2743 904.4c158.1-25.5 243.9-195.8 167.3-332.2-61.2-109-251.7-174.2-374.4-31.5-12.1 14-22.3 17.3-40.1 13.4-144.5-31.7-289.2-62.1-433.9-93-60.1-12.9-88.2 3.6-106.7 62.4-52.2 165.6-104.6 331.2-156.6 496.8-4.5 14.3-8.2 24.8-27.6 24.7-97.6-.6-193.8 13.2-289.2 33.3-171.1 36.2-330.3 100.8-474.9 199.5-13.2 9-21.3 5.3-33.4-.2-49.5-22.9-97.9-51.4-150-65.2-180.4-47.8-394.6 98.8-357.7 339.7 14.5 94.7 70.8 163.2 148.4 215.2 12.7 8.5 15.6 16.2 13.3 30.2-21.8 130.3-8.2 256.7 44.4 378.3 74.8 173 203.4 297.9 359.8 396.5 178.4 112.6 376.3 169.6 583.8 195.7 181.5 22.8 362 14.2 540.3-26.1 256.6-57.9 485.1-168 661-369.7 139.5-160 201.3-346 172.6-558.2-3.8-28.4 3-42.1 24.9-58.4z"/><path d="M3150.5 1481.1c11.1 118.6-44.5 206.6-136.1 274.5-22 16.3-28.8 30-24.9 58.3 28.7 212.2-33.1 398.2-172.6 558.2-175.9 201.7-404.4 311.8-661 369.7-178.3 40.2-358.8 48.8-540.3 26.1-207.5-26.1-405.4-83.1-583.8-195.7-156.3-98.6-285-223.6-359.8-396.5-52.6-121.6-66.1-248-44.4-378.3 2.3-14-.5-21.7-13.3-30.2-77.6-52-133.9-120.5-148.4-215.2-36.9-240.9 177.3-387.5 357.7-339.7 52.2 13.8 100.5 42.3 150 65.2 12.1 5.6 20.2 9.2 33.4.2 144.7-98.7 303.8-163.3 474.9-199.5 95.4-20.2 191.6-34 289.2-33.3 19.3.1 23.1-10.3 27.6-24.7 52-165.7 104.4-331.2 156.6-496.8 18.5-58.8 46.6-75.3 106.7-62.4 144.7 30.9 289.4 61.3 433.9 93 17.9 3.9 28.1.6 40.1-13.4 122.7-142.7 313.1-77.5 374.4 31.5 76.6 136.4-9.2 306.7-167.3 332.2-115 18.5-228.9-54.4-259.5-168.8-6.5-24.2-17.9-32.2-40.8-36.9-111.6-22.7-222.7-47.4-333.9-71.3-8.8-1.9-17.8-2.9-29-4.7-46.6 145-92.7 288.6-140.1 436 28 2.9 52.4 5.5 76.8 8 217.9 22.1 420 89.8 603.4 210.7 11.6 7.6 18.5 7.2 29.8-2 98.9-80.8 209.2-95.1 324.5-44.3 106.5 47.3 165.3 133.8 176.2 250.1zM2221.8 1946c96.7-.1 182.7-85.8 182.3-181.6-.4-102-83.1-185.4-183.3-185.1-97.3.3-182.1 83.5-182 178.4.1 103.8 82.4 188.4 183 188.3zm43.2 347.4c21-24.3 15.6-53.2-3.7-72.3-26.8-26.6-61.4-34-88.3-18.6-10.8 6.2-20.7 13.9-31.3 20.4-185.3 112.6-378.9 130.1-579.5 46.7-41.1-17.1-78.9-42.5-117.6-65-28.8-16.8-65-10.9-92.6 17-18.2 18.3-20.5 49.8-2.3 75.6 10.3 14.5 24.2 27.6 38.8 37.8 125.4 88 266.4 124.7 429.5 126.6 17.7-1.3 46.7-2.9 75.7-5.6 101.4-9.3 198.1-36 285.6-88.6 31.9-19.4 61.1-45.8 85.7-74zM1398.5 1946c90.9-.2 178-89.4 177.6-182-.4-100.4-84.7-184.7-184.5-184.6-98.5 0-182.4 84.6-182.2 183.7.3 100.2 86 183.1 189.1 182.9z" fill="#fff"/><path class="st0" d="M2404.1 1764.4c.4 95.8-85.6 181.5-182.3 181.6-100.6.1-182.9-84.5-183-188.3-.1-95 84.7-178.1 182-178.4 100.2-.4 182.8 83 183.3 185.1zM2261.3 2221.1c19.3 19.1 24.7 48 3.7 72.3-24.6 28.3-53.8 54.7-85.8 73.9-87.5 52.6-184.2 79.3-285.6 88.6-29 2.7-57.9 4.3-75.7 5.6-163.1-1.8-304.1-38.6-429.5-126.6-14.7-10.3-28.5-23.3-38.8-37.8-18.2-25.8-15.9-57.4 2.3-75.6 27.7-27.9 63.8-33.7 92.6-17 38.7 22.6 76.5 47.9 117.6 65 200.6 83.5 394.2 65.9 579.5-46.7 10.6-6.4 20.6-14.1 31.3-20.4 27-15.4 61.6-7.9 88.4 18.7zM1576.2 1763.9c.4 92.6-86.7 181.8-177.6 182-103.2.2-188.8-82.7-189.1-183-.2-99.1 83.7-183.7 182.2-183.7 99.8 0 184.1 84.3 184.5 184.7z"/></g></svg>',

	),
	'telegram'  => array(
		'name'   => 'Telegram',
		'url'    => 'https://telegram.me/share/url?text=%title&url=%url',
		'width'  => 780,
		'height' => 580,
		'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128"><defs><linearGradient id="a" x1="50%" x2="50%" y1="0%" y2="99.258%"><stop offset="0%" stop-color="#2AABEE"/><stop offset="100%" stop-color="#229ED9"/></linearGradient></defs><g fill="none" fill-rule="nonzero"><circle cx="64" cy="64" r="64" fill="url(#a)"/><path fill="#FFF" d="M28.97 63.324c18.657-8.128 31.098-13.487 37.323-16.076 17.774-7.393 21.467-8.677 23.874-8.72.53-.009 1.713.122 2.48.745.648.525.826 1.235.911 1.733.085.498.191 1.633.107 2.52-.963 10.12-5.13 34.677-7.25 46.012-.898 4.796-2.664 6.404-4.375 6.561-3.716.342-6.538-2.456-10.138-4.815-5.633-3.693-8.815-5.991-14.283-9.594-6.319-4.164-2.222-6.453 1.379-10.193.942-.98 17.318-15.874 17.634-17.225.04-.169.077-.799-.297-1.131-.375-.333-.927-.22-1.325-.129-.565.128-9.564 6.076-26.996 17.843-2.554 1.754-4.868 2.609-6.94 2.564-2.286-.05-6.681-1.292-9.95-2.354-4.007-1.303-7.193-1.992-6.915-4.205.144-1.152 1.731-2.33 4.761-3.536z"/></g></svg>',

	),
	'linkedin'  => array(
		'name' => 'LinkedIn',
		'url'  => 'https://www.linkedin.com/shareArticle?mini=true&url=%url&title=%title',
		'url'  => 'https://www.linkedin.com/sharing/share-offsite/?url=%url',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" ><path d="M34 2.5v29a2.5 2.5 0 01-2.5 2.5h-29A2.5 2.5 0 010 31.5v-29A2.5 2.5 0 012.5 0h29A2.5 2.5 0 0134 2.5zM10 13H5v16h5zm.45-5.5a2.88 2.88 0 00-2.86-2.9H7.5a2.9 2.9 0 000 5.8 2.88 2.88 0 002.95-2.81zM29 19.28c0-4.81-3.06-6.68-6.1-6.68a5.7 5.7 0 00-5.06 2.58h-.14V13H13v16h5v-8.51a3.32 3.32 0 013-3.58h.19c1.59 0 2.77 1 2.77 3.52V29h5z" fill="#0966C1"/></svg>',

	),
	'xing'      => array(
		'name'   => 'Xing',
		'url'    => 'https://www.xing.com/app/user?op=share;url=%url;title=%title',
		'width'  => 570,
		'height' => 580,
		'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="2128" height="2500" viewBox="426.896 102.499 170.207 200"><path d="M442.394 142c-1.736 0-3.197.61-3.934 1.803-.76 1.231-.645 2.818.166 4.424l19.503 33.761c.033.064.033.105 0 .164l-30.648 54.084c-.799 1.592-.76 3.191 0 4.425.736 1.187 2.033 1.966 3.771 1.966h28.844c4.312 0 6.393-2.91 7.867-5.57 0 0 29.973-53.01 31.14-55.068-.118-.19-19.83-34.58-19.83-34.58-1.439-2.557-3.606-5.41-8.03-5.41h-28.849z" fill="#005a5f"/><path d="M563.574 102.501c-4.309 0-6.176 2.714-7.723 5.494 0 0-62.14 110.2-64.188 113.818.105.196 40.984 75.191 40.984 75.191 1.432 2.558 3.641 5.494 8.06 5.494h28.81c1.738 0 3.096-.654 3.828-1.843.77-1.23.748-2.857-.059-4.458l-40.664-74.295a.167.167 0 010-.189l63.863-112.92c.803-1.594.82-3.22.061-4.452-.736-1.188-2.098-1.843-3.836-1.843h-29.139v.002h.003z" fill="#d4d600"/></svg>',

	),
	'vk'        => array(
		'name'   => 'VK',
		'url'    => 'https://vk.com/share.php?url=%url&title=%title',
		'width'  => 655,
		'height' => 430,
		'icon'   => '<svg width="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 14.375C0 7.599 0 4.21 2.105 2.105 4.21 0 7.6 0 14.375 0h1.25c6.776 0 10.165 0 12.27 2.105C30 4.21 30 7.6 30 14.375v1.25c0 6.776 0 10.165-2.105 12.27C25.79 30 22.4 30 15.625 30h-1.25c-6.776 0-10.165 0-12.27-2.105C0 25.79 0 22.4 0 15.625v-1.25z" fill="#2787F5"/><path fill-rule="evenodd" clip-rule="evenodd" d="M8.125 9.375H5.938c-.625 0-.75.294-.75.619 0 .579.741 3.453 3.453 7.253 1.807 2.596 4.354 4.003 6.671 4.003 1.391 0 1.563-.313 1.563-.85v-1.962c0-.625.132-.75.572-.75.325 0 .88.162 2.179 1.413 1.483 1.484 1.727 2.149 2.561 2.149h2.188c.625 0 .938-.313.757-.93-.197-.614-.905-1.506-1.845-2.563-.51-.602-1.274-1.251-1.506-1.576-.325-.417-.232-.602 0-.973 0 0 2.665-3.754 2.943-5.029.14-.463 0-.804-.662-.804h-2.187c-.556 0-.813.294-.952.619 0 0-1.112 2.711-2.688 4.472-.51.51-.742.673-1.02.673-.139 0-.34-.163-.34-.626v-4.334c0-.556-.161-.804-.625-.804h-3.438c-.347 0-.556.258-.556.503 0 .527.788.649.869 2.132v3.221c0 .707-.127.835-.406.835-.741 0-2.545-2.724-3.615-5.84-.21-.606-.42-.851-.979-.851z" fill="#fff"/></svg>',

	),
	'whatsapp'  => array(
		'name'   => 'Whatsapp',
		'url'    => 'whatsapp://send?text=%title%20%url',
		'width'  => 655,
		'height' => 430,
		'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="662.234" height="663.504" viewBox="0 0 175.216 175.552"><defs><linearGradient xlink:href="#a" id="b" x1="85.915" y1="32.567" x2="86.535" y2="137.092" gradientUnits="userSpaceOnUse"/><linearGradient id="a"><stop offset="0" stop-color="#57d163"/><stop offset="1" stop-color="#23b33a"/></linearGradient></defs><path d="M54.532 138.45l2.236 1.324c9.387 5.572 20.15 8.518 31.125 8.523h.024c33.707 0 61.14-27.425 61.153-61.135.006-16.335-6.349-31.696-17.895-43.251-11.547-11.555-26.9-17.921-43.235-17.928-33.733 0-61.165 27.423-61.178 61.13-.005 11.55 3.228 22.8 9.349 32.535l1.455 2.312-6.18 22.559zm-40.81 23.544L24.16 123.88c-6.438-11.154-9.825-23.808-9.82-36.772.016-40.555 33.02-73.55 73.577-73.55 19.68.01 38.154 7.67 52.047 21.572 13.89 13.903 21.537 32.383 21.53 52.038-.018 40.552-33.027 73.552-73.577 73.552-.003 0 .003 0 0 0h-.032a73.531 73.531 0 01-35.16-8.954zm0 0" fill="#b3b3b3" filter="url(#filter1769)"/><path d="M12.966 161.238l10.439-38.114c-6.439-11.154-9.826-23.808-9.822-36.772.017-40.555 33.02-73.55 73.578-73.55 19.681.01 38.154 7.67 52.047 21.572 13.89 13.903 21.537 32.383 21.53 52.038-.017 40.553-33.027 73.552-73.577 73.552-.003 0 .003 0 0 0h-.032a73.531 73.531 0 01-35.16-8.954z" fill="#fff"/><path d="M87.184 25.227c-33.733 0-61.165 27.423-61.178 61.13-.005 11.55 3.228 22.8 9.35 32.535l1.454 2.312-6.18 22.56 23.147-6.07 2.235 1.324c9.387 5.572 20.15 8.518 31.125 8.524h.024c33.707 0 61.14-27.426 61.153-61.136.006-16.335-6.348-31.696-17.895-43.25-11.546-11.556-26.9-17.922-43.235-17.93z" fill="url(#linearGradient1780)"/><path d="M87.184 25.227c-33.733 0-61.165 27.423-61.178 61.13-.005 11.55 3.228 22.8 9.349 32.535l1.455 2.312-6.18 22.559 23.147-6.07 2.235 1.325c9.387 5.572 20.15 8.518 31.125 8.523h.024c33.706 0 61.14-27.425 61.153-61.135.006-16.335-6.348-31.696-17.895-43.251-11.547-11.555-26.9-17.921-43.235-17.928z" fill="url(#b)"/><path d="M68.772 55.603c-1.378-3.06-2.827-3.123-4.137-3.177-1.072-.045-2.298-.042-3.523-.042-1.227 0-3.218.46-4.902 2.3-1.685 1.84-6.435 6.286-6.435 15.332 0 9.045 6.588 17.785 7.506 19.013.919 1.226 12.718 20.38 31.405 27.75 15.53 6.123 18.69 4.905 22.061 4.599 3.371-.306 10.877-4.447 12.408-8.74 1.533-4.292 1.533-7.97 1.074-8.74-.46-.767-1.686-1.226-3.525-2.145s-10.877-5.367-12.563-5.98c-1.685-.614-2.91-.92-4.136.92-1.225 1.84-4.746 5.98-5.82 7.206-1.072 1.228-2.144 1.38-3.984.462-1.838-.922-7.76-2.861-14.783-9.124-5.466-4.873-9.155-10.891-10.228-12.73-1.072-1.84-.115-2.835.807-3.752.826-.824 1.839-2.147 2.76-3.22.916-1.074 1.223-1.84 1.835-3.065.613-1.228.307-2.301-.153-3.22-.46-.92-4.032-10.012-5.667-13.647" fill="#fff" fill-rule="evenodd"/></svg>',

	),
);

$mailster_social_services = apply_filters( 'mailster_social_services', $mailster_social_services );
