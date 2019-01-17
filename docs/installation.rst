安装
============

安装 ``php7-sapnwrfc`` 扩展包含两部分.

- 安装 *SAP NW RFC 库*
- 在PHP配置文件中启用扩展

.. _installing-nwrfcsdk:

安装 SAP NW RFC 库
---------------------------------

您可以在http://service.sap.com/rfc-library上找到有关如何编译，
安装和使用SAP NW RFC库的详细说明。

下载 SAP NW RFC 库
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

在 `Software Center on the SAP ONE Support Launchpad <https://launchpad.support.sap.com/#/softwarecenter>`_ 中下载 *SAP NW RFC library*.

搜索 ``SAP NW RFC SDK 7.20`` . 下载适合你平台的版本.

.. image:: /images/search_nwrfcsdk.png
   :alt: Search for SAP NW RFC SDK 7.20

搜索 ``SAPCAR 7.20`` 下载 *SAPCAR* 工具解压SDK.

.. image:: /images/search_sapcar.png
   :alt: Search for SAPCAR 7.20

解压类库到文件夹中. 本指南使用``/usr/sap/nwrfcsdk``（在Linux上）或 ``C:\nwrfcsdk`` （在Windows上）作为默认路径。您可以自由选择其他目录。如果这样做，请确保在下面的说明中
替换正确的路径。

配置类库文件路径
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

您必须将*SAP NW RFC 库* 中的 ``lib`` 文件配置在系统类库搜索范围内.

Linux
"""""

创建文件 ``/etc/ld.so.conf.d/nwrfcsdk.conf`` 内容如下:

::

    /usr/sap/nwrfcsdk/lib

之后运行 ``ldconfig``.

Windows
"""""""

在命令行中执行 ``set PATH=%PATH%;C:\nwrfcsdk\lib``. 这只是临时修改，在命令行窗口关闭后修
改将丢失.

.. note:: 

   下面操作因为Windows版本不同而不一样.

永久添加类库到PATH环境变量，按一下步骤：
    
    1. 打开“开始”菜单，然后输入 ``environment``
    2. 选择 ``Edit the system environment variables`` or ``Edit environment variables for your account``,
       depending on whether you want to set the path for the user or the whole system.
    3. Select the ``PATH`` environment variable, click ``Edit`` and add the path ``C:\nwrfcsdk\lib``.

启用扩展
----------------------

在 ``php.ini`` 中增加一行:

::

    # for Linux/Unix
    extension=sapnwrfc.so

    # for Windows
    extension=php_sapnwrfc.dll

你可以通过查看 ``php -m`` 输出的内容确认扩展是否被加载。显示中应该包含 ``sapnwrfc``.