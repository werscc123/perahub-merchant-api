<?php

$svr_config['user_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19091, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['order_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19093, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['account_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19092, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['active_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['verify_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19095, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['msg_dispatch_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19097, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['fee_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19098, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['pay_gate_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19099, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['quota_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19094, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['manage_auth_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19096, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => 'server',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['manual_acct_order_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['manul_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['settle_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19081, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['topup_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19101, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['f2f_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['offline_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['integral_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['app_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['risk_control_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];

$svr_config['bananapay_server'] = [
    'relay' => [
        'host' => '172.31.13.123', //服务器 172.18.250.244
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];

$svr_config['app_server'] = [
    'relay' => [
        'host' => '172.31.13.123', //服务器 172.18.250.244
        'port' => 22000, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['auth_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19083, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];
$svr_config['watch_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19085, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => '172.31.13.123',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];

$svr_config['config_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19086, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => 'server',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];

$svr_config['merchant_server'] = [
    'thrift' => [
        'host' => '172.31.13.123', //服务器 172.31.13.123
        'port' => 19087, //端口
        'send_timeout' => 5000, //发送超时，单位毫秒
        'recv_timeout' => 10000, //接受超时，单位毫秒
    ],
    'http' => [
        'host' => 'server',
        'port' => 80,
        'domain' => '',
        'scheme' => 'http',
        'connect_timeout' => 5000,
        'read_timeout' => 10000,
    ],
];

return $svr_config;
