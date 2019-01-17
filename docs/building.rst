源码编译
====================

在尝试编译扩展前，确认 *SAP NW RFC library* 已经安装了。
相关说明，请参阅安装 :ref:`installing-nwrfcsdk` 。

在Linux上编译
-----------------

.. code-block:: shell

    $ git clone https://github.com/gkralik/php7-sapnwrfc.git
    $ cd php7-sapnwrfc
    $ phpize
    $ ./configure
    $ make
    $ sudo make install

默认情况下，构建脚本在以下位置查找 *SAP NW RFC library* :

.. code-block:: none

    /usr/sap/nwrfcsdk
    /usr/local/sap/nwrfcsdk
    /usr/local/nwrfcsdk
    /opt/nwrfcsdk

如果你安装类库文件在另一个路径，用 ``./configure`` 替换 ``./configure --with-sapnwrfc=/path/to/rfc-library``.

在Windows上编译
-------------------

我们为Windows用户提供了预编译扩展（请参阅`发布页面 <https://github.com/gkralik/php7-sapnwrfc/releases>`_ ）. 
如果你没有找到你要的版本或者仍然希望自己编译，按照以下说明操作。

设置编译环境
^^^^^^^^^^^^^^^^^^^^^^^^^

确认你有一个可用 `和这里一样的构建环境 <https://wiki.php.net/internals/windows/stepbystepbuild>`_。 

在你配置完环境后，可以尝试自己编译PHP：

.. code-block:: shell

    $ configure --disable-all --enable-cli
    $ nmake
    $ nmake snap

如果运行成功，你可以继续。如果不是，双击检查你的编译环境。

编译扩展
^^^^^^^^^^^^^^^^^^^

确认你没有混淆 x86/x64 （ PHP 和 *SAP NW RFC library* 平台必须匹配）。

.. note::
   1. 本指南假设您要构建x64版本。
   2.我们还假设SDK文件位于 C：\nwrfcsdk中

将 ``C:\nwrfcsdk\include`` 中的所有头文件 (``*.h``)  复制到 ``C:\php-sdk\phpdev\vc14\x64\deps\include``. 
将 ``C:\nwrfcsdk\lib`` 中所有库文件 (``*.lib``) 复制到 ``C:\php-sdk\phpdev\vc14\x64\deps\lib.``

下载最新扩展并解压到 ``C:\php-sdk\phpdev\vc14\x64\php-7.0-src\ext\sapnwrfc``。

打开VS2015的开发人员命令提示符，然后按照PHP Windows逐步构建页面中的说明执行步骤1-4的命令。
打开 *VS2015的开发人员命令窗口* ，然后按照 `PHP Windows逐步构建页面 <https://wiki.php.net/internals/windows/stepbystepbuild>`_ 中的说明执行步骤1-4的命令。

必须使用以下命令之一替换配置命令（步骤6）（这具体取决于您要构建的版本）:

.. code-block: none

    // for NTS (shared module)
    configure --disable-all --enable-cli --disable-zts --with-sapnwrfc=shared
    // for NTS (compile in)
    configure --disable-all --enable-cli --disable-zts --with-sapnwrfc

    // for TS (shared module)
    configure --disable-all --enable-cli --with-sapnwrfc=shared
    // for TS (compile in)
    configure --disable-all --enable-cli --with-sapnwrfc

然后继续步骤7-9。

如果您构建了共享扩展，则扩展文件应位于以下位置之一：

.. code-block: none

    C:\php-sdk\phpdev\vc14\x64\php-7.0-src\x64\Release\php-7.0\ext for the NTS version
    C:\php-sdk\phpdev\vc14\x64\php-7.0-src\x64\Release_TS\php-7.0\ext for the TS version