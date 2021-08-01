### 安装 UnixODBC

```bash
yum install unixODBC unixODBC-devel libtool-ltdl libtool-ltdl-devel
```

### 下载&安装连接器

- Hive Connector: https://www.cloudera.com/downloads/connectors/hive/odbc/2-6-9.html
- Impala Connector: https://www.cloudera.com/downloads/connectors/impala/odbc/2-6-9.html

```bash
# 查看是否已安装
rpm -qa | grep ClouderaImpalaODBC
rpm -qa | grep ClouderaHiveODBC

# 卸载原有
rpm -e ClouderaImpalaODBC*
rpm -e ClouderaHiveODBC*

# 安装最新
rpm -ivh ClouderaImpalaODBC-2.6.9.1009-1.x86_64.rpm
rpm -ivh ClouderaHiveODBC-2.6.9.1009-1.x86_64.rpm
```

### 设置环境变量

修改 `/etc/profile`，尾部追加：

```bash
echo 'export LD_LIBRARY_PATH=/usr/local/lib:/opt/cloudera/impalaodbc/lib/64:$LD_LIBRARY_PATH' >> /etc/profile
```

然后使之立即生效：

```bash
source /etc/profile
```
 
如果是 `MacOS`，请加到 `~/.zshrc` 的尾部：

```
export DYLD_LIBRARY_PATH=$DYLD_LIBRARY_PATH:/usr/local/lib
```

测试 `unixODBC` 安装是否成功

```bash
odbcinst -j
```

生成初始化文件

```bash
odbcinst -i -s -l -f /etc/odbcinst.ini
```

### 设置 ODBC 数据源

修改 `/etc/odbc.ini`（完整参数可以参考 `/opt/cloudera/impalaodbc/Setup/odbc.ini`）

```ini
[ODBC]

[ODBC Data Sources]
ImpalaOnCDH=Cloudera ODBC Driver for Impala 64-bit
HiveOnCDH=Cloudera ODBC Driver for Hive 64-bit

[ImpalaOnCDH]
Driver=/opt/cloudera/impalaodbc/lib/64/libclouderaimpalaodbc64.so
Database=default

[HiveOnCDH]
Driver=/opt/cloudera/hiveodbc/lib/64/libclouderahiveodbc64.so
Schema=default
```

如果是 MacOS ，则路径为 `/usr/local/etc/odbc.ini`，内容如下：

```ini
[ODBC]

[ODBC Data Sources]
ImpalaOnCDH=Cloudera ODBC Driver for Impala 64-bit
HiveOnCDH=Cloudera ODBC Driver for Hive 64-bit

[ImpalaOnCDH]
Driver=/opt/cloudera/impalaodbc/lib/universal/libclouderaimpalaodbc.dylib
Database=default

[HiveOnCDH]
Driver=/opt/cloudera/hiveodbc/lib/universal/libclouderahiveodbc.dylib
Schema=default
```

最后，用命令行测试一下：

```
isql -v ImpalaOnCDH 用户名 密码
isql -v HiveOnCDH 用户名 密码
```

关于更详细的 `isql` 命令的用法可参考：https://www.mankier.com/1/isql

### 安装 PHP 扩展 odbc.so 

#### 对于 PHP 7.2.6

```bash
cd /soft/lnmp1.5-full/src/php-7.2.6/ext/odbc
phpize
./configure --with-php-config=/usr/local/php/bin/php-config --with-unixODBC=/usr/
make && make install
echo "extension=odbc.so" >> /usr/local/php/etc/php.ini
/etc/init.d/php-fpm reload
```

#### 对于 PHP 7.4.7

```bash
cd /soft/lnmp1.7-full/src/php-7.4.7/ext/odbc
phpize
./configure --with-php-config=/usr/local/php/bin/php-config --with-unixODBC=/usr/
make && make install
echo "extension=odbc.so" >> /usr/local/php/etc/php.ini
/etc/init.d/php-fpm reload
```

如果在 `./configure` 过程中报以下错误：

```
checking for Adabas support... cp: cannot stat '/usr/local/lib/odbclib.a': No such file or directory
configure: error: ODBC header file '/usr/local/incl/sqlext.h' not found!
```

则需修改 `configure` 文件后再重试即可：

```bash
sed -ri 's@^ *test +"\$PHP.* *= *"no" *&& *PHP_.*=yes *$@#&@g' configure
```

### 安装 PHP 扩展 pdo_odbc.so

> 本步非必须，因为直接用 `odbc_*` 系列函数已可以满足需求了。

```bash
cd ~/soft/lnmp1.5-full/src/php-7.2.6/ext/pdo_odbc
phpize
./configure --with-php-config=/usr/local/php/bin/php-config --with-pdo-odbc=unixODBC,/usr
make && make install
echo "extension=pdo_odbc.so" >> /usr/local/php/etc/php.ini
/etc/init.d/php-fpm reload
```

### 参考文档

- https://blog.csdn.net/cpainter/article/details/87345629
- https://sskaje.me/2014/07/php-odbc-connect-cloudera-impala-hive/