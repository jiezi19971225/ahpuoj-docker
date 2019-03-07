### AHPUOJ
基于hustoj二次开发的oj
### 安装方式
使用docker镜像方式安装，使用了docker-compose工具一键式部署，安装oj前请先安装以上工具
### 安装步骤
克隆仓库到本地
```
git clone https://github.com/jiezi19971225/ahpuoj-docker.git 
```
切换目录
```
        cd ahpuoj-docker/docker
```
构建镜像
```
        docker-compose build
```
创建并且容器
 ```
        docker-compose up -d
```
进入容器
```
        docker-compose exec ahpuoj bash
```
创建容器后手动运行安装脚本
```
        sh src/install/install.sh
```
这样就安装完成了
### 管理员注册
默认将容器的80端口映射到主机的8080端口，访问localhost:8080，进入oj系统。创建一个用户名为admin的用户，作为管理员。
### 语言支持
目前支持C，C++，JAVA，PYTHON，Go
### 使用说明
[使用说明](https://blog.csdn.net/qq_38923784/article/details/82918221)



