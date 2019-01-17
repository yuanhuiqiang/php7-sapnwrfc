Introduction
==============

`php7-sapnwrfc` 扩展封装了 *SAP NetWeaver RFC 库* 并且使用提供的方法允许
PHP开发者调用开启了远程调用的ABAP模块。

如果您没有使用SAP NW RFC SDK的经验，强烈建议您阅读以下文章的文章：

- `SAP NetWeaver RFC SDK (SAP Help) <https://help.sap.com/saphelp_nw73ehp1/helpdata/en/48/a88c805134307de10000000a42189b/frameset.htm?frameset=/en/48/a994a77e28674be10000000a421937/frameset.htm>`_
- `SAP NetWeaver RFC SDK -- RFC Client Programs <https://wiki.scn.sap.com/wiki/display/ABAPConn/SAP+NetWeaver+RFC+SDK+--+RFC+Client+Programs>`_
- `SAP NetWeaver RFC SDK -- Advanced Topics <https://wiki.scn.sap.com/wiki/display/ABAPConn/SAP+NetWeaver+RFC+SDK+--+Advanced+Topics>`_

.. _quick-start:

快速开始
------------

下面是调用启用RFC的ABAP模块 ``STFC_CHANGING`` 并打印返回值的简单案例：

.. code-block:: php

    <?php

    use SAPNWRFC\Connection as SapConnection;
    use SAPNWRFC\Exception as SapException;

    $config = [
        'ashost' => 'my.sap.system.local',
        'sysnr'  => '00',
        'client' => '123',
        'user'   => 'YOUR USERNAME',
        'passwd' => 'YOUR PASSWORD',
        'trace'  => SapConnection::TRACE_LEVEL_OFF,
    ];

    try {
        $c = new SapConnection($config);

        $f = $c->getFunction('STFC_CHANGING');
        $result = $f->invoke([
            'START_VALUE' => 0,
            'COUNTER' => 1,
        ]);

        var_dump($result);
        /*
        * array(2) {
        *   ["COUNTER"]=>
        *   int(2)
        *   ["RESULT"]=>
        *   int(1)
        *   }
        */
    } catch(SapException $ex) {
        echo 'Exception: ' . $ex->getMessage() . PHP_EOL;
    }

阅读 :doc:`usage guide </usage>` ，了解所提供接口的详细说明。

使用场景
---------------

至今不是 *SAP NW RFC SDK* 中所有的功能都在扩展中实现了。基本上,我们区分两种情况：

- 客户端: 使用PHP代码调用ABAP程序
- 服务端: 通过ABAP客户端调用PHP代码

目前，扩展只支持客户端场景，即通过PHP调用ABAP函数模块。

当前不支持服务端场景。

在该扩展中其他功能可能也不能使用。如果有特殊需要，随时在GitHub上 `打开问题  <https://github.com/gkralik/php7-sapnwrfc/issues>`_ 或 `拉取请求 <https://github.com/gkralik/php7-sapnwrfc/pulls>`_ 。