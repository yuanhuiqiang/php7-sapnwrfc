使用
=====

本指南介绍了 ``php7-sapnwrfc`` 扩展的常用功能。有关可用方法的更详细概述，请参阅 :doc:`API overview </api>`。

要立即开始使用，请参阅 :ref:`quick-start`。

连接SAP系统
--------------------------

连接SAP系统，我们需要传递两个连接参数创建一个 ``SAPNWRFC\Connection`` 实例:

.. code-block:: php

    <?php
    $parameters = [
        'ashost' => 'my.sap.system.local',
        'sysnr'  => '00',
        'client' => '123',
        'user' => 'DEMO',
        'passwd' => 'XXXX',
        // 如果您需要通过saprouter连接，请取消下行注释
        //'saprouter' => '/H/my.saprouter.local/H/',
    ];

    // 连接
    $connection = new SAPNWRFC\Connection($parameters);

    // 处理

    // 关闭连接
    $connection->close();

如果连接尝试失败，则抛出 ``SAPNWRFC\ConnectionException`` ，其中包含有关错误的详细信息。

我们还可以使用 ``sapnwrfc.ini`` 件来指定连接详细信息。有关详细信息，请参阅 :doc:`API overview </api>` 中的 
``SAPNWRFC\Connection::setIniPath($path)`` 和 ``SAPNWRFC\Connection::reloadIniFile()`` 。

有关可用的连接参数，请参阅 `sapnwrfc.ini参数概述 <https://help.sap.com/viewer/753088fc00704d0a80e7fbd6803c8adb/7.5.9/en-US/48ce50e418d3424be10000000a421937.html>`_ 。

在使用完连接后，建议使用 ``SAPNWRFC\Connection::close()`` 关闭连接。

连接选项
^^^^^^^^^^^^^^^^^^

.. versionadded:: 1.2.0

 ``SAPNWRFC\Connection`` 使用了第二个（可选）参数来指定连接。

.. code-block:: php

    <?php

    $parameters = [ /* <...> */ ];

    $options = [
        'use_function_desc_cache' => false,
    ];

    $connection = new SAPNWRFC\Connection($parameters, $options);

可以使用以下选项：

use_function_desc_cache
    如果设置为 ``false`` ，则在使用 ``Connection::getFunction()`` 查找函数之前，将清除本地函数目标缓存。
    如：仍在运行时后端功能模块签名发生更改，此时该功能非常有用。因为如果脚本继续使用旧的（缓存的）函数描述，
    则会导致垃圾/缺失值。

.. note::

    将此选项设置为 ``false`` 可能会导致性能下降，因为每次调用 ``Connection::getFunction()`` 时都必须从后端获取函数描述。

.. seealso::

    有关清除各个功能模块的高速缓存的方法，请参阅 :ref:`manually_clearing_function_desc_cache` 。

    *默认值:* ``true``

调用远程函数模块
-------------------------------


查找功能模块 
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

在我们调用远程功能模块之前，我们必须查找所需的函数模块以返回 ``SAPNWRFC\RemoteFunction`` 对象。
这是通过 ``SAPNWRFC\Connection::getFunction($functionName)`` 方法完成的。我们只需传递远程功能模块的名称：

.. code-block:: php

    <?php
    
    $remoteFunction = $connection->getFunction('RFC_PING');

如果查找成功，则返回 ``SAPNWRFC\RemoteFunction`` 类型的对象，该对象可用于调用该函数。

如果函数查找失败，则抛出 ``SAPNWRFC\FunctionCallException`` 异常。

调用函数模块
^^^^^^^^^^^^^^^^^^^^^^^^^^^

在获取到 ``SAPNWRFC\RemoteFunction`` 对象之后，我们可以使用 ``invoke()`` 方法调用该函数模块。

要调用函数模块 ``RFC_PING``，我们可以简单地调用不带参数的 ``invoke()``。

.. code-block:: php

    <?php

    $returnValue = $remoteFunction->invoke();

如果函数模块有返回值，则调用 ``invoke()`` 将会返回它们。发生任何错误上，将抛出 ``SAPNWRFC\FunctionCallException`` 异常。

参数和返回值
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

实际上，大多数远程功能模块都要求我们传递参数和/或在调用后返回参数。

通过使用 ``$parameters`` 数组传递可能需要的参数给函数的 ``invoke()`` 方法。键是参数的名称，值是要传递的值。

返回值也是如此。如果远程功能模块有返回值，它们将通过 ``invoke()`` 返回一个数组。

.. note::

    ABAP功能模块知道四种不同类型的参数：

    - ``IMPORT``: 由客户端设定
    - ``EXPORT``: 由服务端设定
    - ``CHANGING``:  由客户端设定并且可被服务端修改
    - ``TABLE``:  由客户端设定并且可被服务端修改

    调用需要传参的函数模块 ``STFC_CHANGING`` 以及向调用者返回参数，我们可以执行以下操作：

.. code-block:: php

    <?php

    $function = $connection->getFunction('STFC_CHANGING');
    $result = $function->invoke([
        'START_VALUE' => 0,
        'COUNTER' => 1,
    ]);

    /*
    $result looks like this:

    array(2) {
      ["COUNTER"] => int(2)
      ["RESULT"] => int(1)
    }
    */

参数和返回值的类型映射到标准PHP类型。

参数类型映射
^^^^^^^^^^^^^^^^^^^^^^^

远程功能模块执行ABAP代码，因此使用ABAB数据类型。此扩展在RFC数据类型和内置PHP数据类型之间进行映射，如下所示：

+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| ABAP type | RFC type | PHP type  | Meaning                                           | Notes                                          |
+===========+==========+===========+===================================================+================================================+
| C         | CHAR     | string    | Text field (alphanumeric characters)              | right-padded with blanks; see ``rtrim`` option |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| D         | DATE     | string    | Date field (format: YYYYMMDD)                     |                                                |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| T         | TIME     | string    | Time field (format: HHMMSS)                       |                                                |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| X         | BYTE     | string    | Hexadecimal field                                 | use ``hex2bin()`` to convert to binary         |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| N         | NUM      | string    | Numeric text field                                |                                                |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| STRING    | STRING   | string    | String (dynamic length)                           |                                                |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| XSTRING   | BYTE     | string    | Hexadecimal string (dynamic length)               | use ``hex2bin()`` to convert to binary         |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| I         | INT      | integer   | Integer                                           | INT1 and INT2 are also mapped to integer       |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| P         | BCD      | double    | Packed number / BCD                               |                                                |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+
| F         | FLOAT    | double    | Floating point number                             |                                                |
+-----------+----------+-----------+---------------------------------------------------+------------------------------------------------+

此外，还有表格和结构：

- 结构体映射到关联数组，键是字段名称，值是字段值。
- 内表是个数组结构

调用函数模块时传递可选参数
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

使用 ``RemoteFunction::invoke()`` 调用函数模块时，可以传递第二个参数，指定函数调用的选项。

.. code-block:: php

    <?php

    // ...
    $options = [
        'rtrim' => true
    ];

    $function->invoke($parameters, $options);

可以使用以下选项：

rtrim
    在ABAP中，有两种方法可以存储字符串：固定长度字符串类型 C 或动态长度类型 STRING 。当使用 C 类字符串时，
    如果字符串短于预定义的长度，则使用空格右边填充字符串。要使用字符串统一扩展行为，可以使用rtrim选项。如果
    设置为true，则在返回之前键入 C 字符串进行右边trim。 

    *默认值：* ``false``

激活/取消激活参数
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

在一个函数模块中 *SAP NW RFC library* 支持激活和取消激活功能模块的参数。如果函数模块具有许多（可能很大的）
您不感兴趣的返回值时，这将非常有用。

要激活或取消激活参数，我们在远程函数对象上调用方法
``SAPNWRFC\RemoteFunction::setParameterActive($parameterName, $isActive)``。
我们可以使用 ``SAPNWRFC\RemoteFunction::isParameterActive($parameterName)`` 来检查参数是否处于活动状态。

.. code-block:: php

    <?php

    $function = $connection->getFunction('STFC_STRUCTURE');

    $function->setParameterActive('IMPORTSTRUCT', false);
    $function->setParameterActive('RFCTABLE', false);

    $function->isParameterActive('IMPORTSTRUCT'); // returns false

    // 在调用函数模块时我们不需要传递参数,
    // 因为我们取消激活所有
    $result = $function->invoke([]);

    // $result will not contain the 'RFCTABLE' parameter

    $function->setParameterActive('RFCTABLE', true);
    $function->isParameterActive('IMPORTSTRUCT'); // returns true

    // we need to pass the 'RFC_TABLE' parameter now
    $result = $function->invoke([
        'RFCTABLE' => [],
    ]);


.. _manually_clearing_function_desc_cache:

手动清除功能模块描述缓存
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. versionadded:: 1.3.0

对 ``Connection::getFunction()`` 的调用使用了本地的函数模块描述缓存来加速查找。通常是希望这么做的，但是当脚本在运行时，在
后端修改函数模块签名可能会导致意外结果（缺少/垃圾返回值等）。

除了在连接级别设置 ``use_function_desc_cache`` 选项外，还可以使用该函数
 ``\SAPNWRFC\clearFunctionDescCache($functionName, $repositoryId = null)`` 清除各个函数模块的缓存。

.. code-block:: php

    <?php

    \SAPNWRFC\clearFunctionDescCache('STFC_STRUCTURE');
    // or using the system ID
    \SAPNWRFC\clearFunctionDescCache('STFC_STRUCTURE', 'X01');

.. warning::

    手动清除函数描述缓存不会影响现有的 ``RemoteFunction`` 对象:

    .. code-block:: php

        <?php

        $oldFn = $connection->getFunction('STFC_STRUCTURE');
        \SAPNWRFC\clearFunctionDescCache('STFC_STRUCTURE');
        $newFn = $connection->getFunction('STFC_STRUCTURE');

        // $oldFn 仍然使用旧的功能描述！


函数模块细节
^^^^^^^^^^^^^^^^^^^^^^^

 ``SAPNWRFC\RemoteFunction`` 对象定义一个 ``name`` 属性，该属性包含它所代表的功能模块的名称。

此外，在对象上定义函数模块的每个参数的属性，该属性可用于获取有关参数的详细信息。

Trace 级别
------------

*SAP NW RFC library* 允许创建跟踪文件以解决连接和/或函数调用问题。

我们可以在建立连接时通过 ``trace`` 参数设置所需的跟踪级别，也可以使用
``SAPNWRFC\Connection::setTraceLevel($level)`` 方法随时更改它。

该扩展为 ``SAPNWRFC\Connection`` 类定义了四个跟踪级别（从最小到最详细）的常量： ``TRACE_LEVEL_OFF``,
``TRACE_LEVEL_BRIEF``, ``TRACE_LEVEL_VERBOSE``, ``TRACE_LEVEL_FULL``.

此外，我们可以使用 ``SAPNWRFC\Connection::setTraceDir($path)`` 为生成的跟踪文件设置目录。跟踪文件的默认位置是当前工作目录。

获取版本
-------------------

该扩展提供了 ``SAPNWRFC\Connection::version()`` 和 ``SAPNWRFC\Connection::rfcVersion()`` 方法，用于获取正在使用的扩展版本和RFC SDK版本。

两种方法都返回一个格式为 ``MAJOR.MINOR.PATCH`` 的字符串（例如1.1.3）;

异常
----------

如果在任何交互期间发生错误，则会引发异常并显示错误详细信息。目前，有两个异常类：

- ``SAPNWRFC\ConnectionException`` 用于与连接本身有关的任何错误。
- ``SAPNWRFC\FunctionCallException`` 用于源自函数模块调用的错误。

这两个异常类都扩展了基本异常类 ``SAPNWRFC\Exception`` ，它为 ``RuntimeException`` 提供的标准方法和属性添加了一个
额外的 ``getErrorInfo()`` 方法。

``getErrorInfo()`` 返回一个包含详细错误信息的数组，至少包含 ``code``, ``key`` 和 ``message``. 

根据错误类型，在详细信息中以下额外的键可能被使用： ``abapMsgClass``, ``abapMsgType``, ``abapMsgNumber``, 
``abapMsgV1``, ``abapMsgV2``, ``abapMsgV3``, ``abapMsgV4``.
