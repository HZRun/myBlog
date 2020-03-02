# -*- coding: utf-8 -*-

import os
import jieba
import io

test_content ="操作ILP(Interledger)预共享密钥V2（PSKv2）传输协议Python3-使用User Agent和代理IP隐藏身份手把手教Apereo CAS5.2.3服务端Server的开发环境以太坊（ETH）挖矿教程，minerOS系统Xshell登录阿里云服务器ECS微信小程序之PHP服务器搭建MFC用户名和密码的登录界面设计golang 如何进行jwt权限验证微信支付HTTPS服务器证书更换问题维护篇 21. 加强防火墙登录的安全 ? 飞塔 (Fortinet) 防火墙Hive 五种数据导入方式介绍启动hadoop 2.6遇到的datanode启动不了hadoop入门--简单的MapReduce案例HBase集群安装部署（完全分布式）zookeeper文档（1）----zookeeper集群搭建OpenStack实战教程|OpenStack入门到精通视频教程linux下配置yum源详解CentOS6.5下docker的安装及遇到的问题和简单使用Hadoop面试45个题目和参考答案spring 源码如何导入到eclipsetomcat7日志管理--基础知识、配置、以及使用log4j做日志分割制作各种docker镜像TensorFlow学习笔记（8）--网络模型的保存和读取Numpy学习（3）：将mnist数据文件读入到数据结构（numpy数组）中算法理解-粒子群算法（一个计算例子）tensorflow学习笔记（三十六）：learning rate decay周志华《机器学习》课后习题解答系列（二）：Ch1 - 绪论TensorFlow笔记之MNIST例程详解Numpy学习（2）：将cifar10/100数据文件读入到python数据结构（字典）中Python pyocr和Tesseract-OCR的安装以及使用推荐给初学LSTM或者懂个大概却不完全懂的人Tensorflow入门：数据结构和编程思想深度学习在计算机视觉领域的前沿进展Deep Learning-TensorFlow (4) CNN卷积神经网络_CIFAR-10进阶图像分类模型(上)阅读源码遇到的一些TF、keras函数及问题2（--小白笔记）独热编码（One-Hot Encoding）介绍及实现tensorflow1.x版本rnn生成cell 报错解决方案随机森林的原理分析及Python代码实现【机器学习】用libsvm C++训练SVM模型[Python ]小波变化库——Pywalvets 学习笔记基于亚像素的边缘检测方法白话空间统计二十四：地理加权回归（四）阿里云centos7.2部署笔录--使用ftp（一）阿里云 linux服务器无法访问80端口的解决办法最新VIP视频解析网站搭建教程（附源码）nodejs中常用加密算法基于仿射传播聚类和最近邻算法的WiFi室内定位源码，采集java，c#，定位matlab干货：计算机网络知识总结使用EMQ搭建MQTT服务器APP安全在线检测--持续更新TheFatRat自动化渗透工具腾讯云搭载frp服务端-映射本地客户端到外网(小米路由pro内网穿透)docker与虚拟机性能比较hadoop2.6.0版本搭建伪分布式环境Docker学习笔记（3）-- 如何使用Dockerfile构建镜像Hadoop Yarn 框架原理及运作机制DOCKER windows 7 详细安装教程创建支持SSH服务的Docker镜像Ubuntu上装KVM：安装、初次使用为elastic添加中文分词Hadoop HDFS文件系统通过java FileSystem 实现上传下载等【Java】JDK的下载、安装与部署Hive用户接口（二）—使用Hive JDBC驱动连接Hive操作实例Hive用户接口（一）—Hive Web接口HWI的操作及使用教你如何配置eclipse环境变量以及注意事项查看Zookeeper日志VMware workstation 虚拟机兼容性问题Namenode HA原理详解（脑裂）SDN环境搭建（mininet，OVS，ryu安装及命令）Visual St"



"""
1. 预处理


2. 中文分词
"""


def savefile(savepath, content):
    with io.open(savepath, mode = 'w',encoding = 'utf-8',errors = 'ignore') as fp:
        fp.write(content)



def readfile(path):
    with io.open(path,mode = 'r',encoding = 'utf-8',errors = 'ignore') as fp:
       content = fp.read()
       return content

#
# corpus_path = "C:/xampp/htdocs/myblog/cate/data/1_data"  # 未分词分类预料库路径
# seg_path = "C:/xampp/htdocs/myblog/cate/data/2_data"  # 分词后分类语料库路径

corpus_path = "C:/xampp/htdocs/myblog/cate/test/1_test"  # 未分词分类预料库路径
seg_path = "C:/xampp/htdocs/myblog/cate/test/2_test"  # 分词后分类语料库路径

catelist = os.listdir(corpus_path)  # 获取改目录下所有子目录
if '.DS_Store' in catelist:catelist.remove('.DS_Store')
for mydir in catelist:
    class_path = corpus_path + "/" + mydir  # 拼出分类子目录的路径
    seg_dir = seg_path +  "/" +mydir + "/"  # 拼出分词后预料分类目录
    if not os.path.exists(seg_dir):  # 是否存在，不存在则创建
        os.makedirs(seg_dir)
    file_list = os.listdir(class_path)
    if '.DS_Store' in file_list:file_list.remove('.DS_Store')
    for file_path in file_list:
        print file_path
        fullname = class_path + '/'+file_path
        print fullname
        content = readfile(fullname).strip()  # 读取文件内容
        content = content.replace("\r\n", "").strip()  # 删除换行和多余的空格
        content_seg = jieba.cut(content)
        savefile(seg_dir + file_path, " ".join(content_seg))

print("分词结束")

# 为了后续生成词向量空间模型的方便，这些分词后的文本信息还要转换成文本向量信息并对象化，利用了Scikit - Learn库的Bunch数据结构，具体代码如下：



import os

import pickle

from sklearn.datasets.base import Bunch


# Bunch 类提供了一种key，value的对象形式

# target_name 所有分类集的名称列表

# label 每个文件的分类标签列表

# filenames 文件路径

# contents 分词后文件词向量形式

def readfile(path):
    with io.open(path, "r", encoding='utf-8', errors='ignore') as fp:

       content = fp.read()

    return content


bunch = Bunch(target_name=[], label=[], filenames=[], contents=[])

# wordbag_path="C:/xampp/htdocs/myblog/cate/train_word_bag/train_set.dat"
#
# seg_path="C:/xampp/htdocs/myblog/cate/data/2_data"

wordbag_path="C:/xampp/htdocs/myblog/cate/test_word_bag/test_set.dat"

seg_path="C:/xampp/htdocs/myblog/cate/test/2_test"

catelist =os.listdir(seg_path)
if '.DS_Store' in catelist: catelist.remove('.DS_Store')
bunch.target_name.extend(catelist)  # 将类别信息保存到Bunch对象

for mydir in catelist:

    class_path = seg_path +"/"+ mydir

    file_list = os.listdir(class_path)
    if '.DS_Store' in file_list: file_list.remove('.DS_Store')
    for file_path in file_list:
        fullname = class_path + '/'+file_path

        bunch.label.append(mydir)  # 保存当前文件的分类标签

        bunch.filenames.append(fullname)  # 保存当前文件的文件路径

        bunch.contents.append(test_content.strip())  # 保存文件词向量

# Bunch对象持久化

with open(wordbag_path, "wb") as file_obj:

   pickle.dump(bunch, file_obj)


print("构建文本对象结束")

""""
3.
向量空间模型


4.
权重策略：TF - IDF方法


"""

import os

from sklearn.datasets.base import Bunch

import pickle  # 持久化类

from sklearn import feature_extraction

from sklearn.feature_extraction.text import TfidfTransformer  # TF-IDF向量转换类

from sklearn.feature_extraction.text import TfidfVectorizer  # TF-IDF向量生成类


def readbunchobj(path):
    file_obj = open(path, "rb")

    bunch = pickle.load(file_obj)

    file_obj.close()

    return bunch


def writebunchobj(path, bunchobj):
    file_obj = open(path, "wb")

    pickle.dump(bunchobj, file_obj)

    file_obj.close()


def readfile(path):
    fp = io.open(path, "r", encoding='utf-8', errors='ignore')

    content = fp.read()

    fp.close()

    return content


path = "C:/xampp/htdocs/myblog/cate/train_word_bag/train_set.dat"

bunch = readbunchobj(path)

# 停用词

stopword_path = "C:/xampp/htdocs/myblog/cate/train_word_bag/stop_words.txt"

stpwrdlst = readfile(stopword_path).splitlines()

# 构建TF-IDF词向量空间对象

tfidfspace = Bunch(target_name=bunch.target_name, label=bunch.label, filenames=bunch.filenames, tdm=[], vocabulary={})

# 使用TfidVectorizer初始化向量空间模型

vectorizer = TfidfVectorizer(stop_words=stpwrdlst, sublinear_tf=True, max_df=0.5)

transfoemer = TfidfTransformer()  # 该类会统计每个词语的TF-IDF权值

# 文本转为词频矩阵，单独保存字典文件

tfidfspace.tdm = vectorizer.fit_transform(bunch.contents)

tfidfspace.vocabulary = vectorizer.vocabulary_

# 创建词袋的持久化

space_path = "C:/xampp/htdocs/myblog/cate/train_word_bag/tfidfspace.dat"

writebunchobj(space_path, tfidfspace)
"""
5.
使用朴素贝叶斯分类模块
上面进行操作的都是训练集的数据，下面是测试集（抽取字训练集），训练步骤和训练集相同，首先是分词，之后生成词向量文件，直至生成词向量模型
"""


import os
import io
from sklearn.datasets.base import Bunch

import pickle  # 持久化类

from sklearn import feature_extraction

from sklearn.feature_extraction.text import TfidfTransformer  # TF-IDF向量转换类

from sklearn.feature_extraction.text import TfidfVectorizer  # TF-IDF向量生成类

# from TF_IDF import space_path


def readbunchobj(path):
    file_obj = open(path, "rb")

    bunch = pickle.load(file_obj)

    file_obj.close()

    return bunch


def writebunchobj(path, bunchobj):
    file_obj = open(path, "wb")

    pickle.dump(bunchobj, file_obj)

    file_obj.close()


def readfile(path):
    fp = io.open(path, "r", encoding='utf-8', errors='ignore')

    content = fp.read()

    fp.close()

    return content


# 导入分词后的词向量bunch对象

path = "C:/xampp/htdocs/myblog/cate/test_word_bag/test_set.dat"

bunch = readbunchobj(path)

# 停用词

stopword_path = "C:/xampp/htdocs/myblog/cate/train_word_bag/stop_words.txt"

stpwrdlst = readfile(stopword_path).splitlines()

# 构建测试集TF-IDF向量空间

testspace = Bunch(target_name=bunch.target_name, label=bunch.label, filenames=bunch.filenames, tdm=[], vocabulary={})

# 导入训练集的词袋

trainbunch = readbunchobj("C:/xampp/htdocs/myblog/cate/train_word_bag/tfidfspace.dat")

# 使用TfidfVectorizer初始化向量空间

vectorizer = TfidfVectorizer(stop_words=stpwrdlst, sublinear_tf=True, max_df=0.5, vocabulary=trainbunch.vocabulary)

transformer = TfidfTransformer();

testspace.tdm = vectorizer.fit_transform(bunch.contents)

testspace.vocabulary = trainbunch.vocabulary

# 创建词袋的持久化

space_path = "C:/xampp/htdocs/myblog/cate/test_word_bag/testspace.dat"

writebunchobj(space_path, testspace)

# 下面执行多项式贝叶斯算法进行测试文本分类并返回精度：



import pickle

from sklearn.naive_bayes import MultinomialNB  # 导入多项式贝叶斯算法包


def readbunchobj(path):
    file_obj = open(path, "rb")

    bunch = pickle.load(file_obj)

    file_obj.close()

    return bunch


# 导入训练集向量空间

trainpath = "C:/xampp/htdocs/myblog/cate/train_word_bag/tfidfspace.dat"

train_set = readbunchobj(trainpath)

# d导入测试集向量空间

testpath = "C:/xampp/htdocs/myblog/cate/test_word_bag/ testspace.dat"

test_set = readbunchobj(testpath)

# 应用贝叶斯算法

# alpha:0.001 alpha 越小，迭代次数越多，精度越高

clf = MultinomialNB(alpha=0.001).fit(train_set.tdm, train_set.label)

# 预测分类结果

predicted = clf.predict(test_set.tdm)

total = len(predicted);
rate = 0

for flabel, file_name, expct_cate in zip(test_set.label, test_set.filenames, predicted):

    print file_name
    if flabel != expct_cate:
        rate += 1
        print(expct_cate)

