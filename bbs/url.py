#coding:UTF-8
import winsound
import httplib2
import threading
import Queue
import os
import time
import MySQLdb
import socks

class myThread(threading.Thread):
	def __init__ (self,queue):
		threading.Thread.__init__(self)
		self.queue = queue

	def run (self):
		keywords={'美食':'url_meishi','咖啡厅':'url_kafeiting', '酒吧':'url_jiuba', '茶馆':'url_chaguan', '宾馆酒店':'url_binguan', '洗浴':'url_xiyu', '足疗':'url_zuliao', '按摩':'url_anmo', 'KTV':'url_ktv', '夜总会':'url_yzh', '娱乐城':'url_ylc', '舞厅':'url_wt'}
		headers = {'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)'}
		#h = httplib2.Http(proxy_info = httplib2.ProxyInfo(socks.PROXY_TYPE_SOCKS5, 'localhost', 8580))
		h = httplib2.Http()
		while (not self.queue.empty()):
			#try:
				row = self.queue.get()
				#print(urow)
				id = str(row[0])
				keyword = row[1]
				url = str(row[2])
				file = "e:/"+keywords[keyword]+"/" + id + '.html'
				if not os.path.exists(file):
					#url = "http://dealer.autohome.com.cn/pr" + pid + "_c" + cid + "_p" + page + ".html"
					resp, content = h.request(url, 'GET', headers=headers)
					open(file,'wb').write(content)
					print(u'正在下载', url)
					
					#content = content.decode('gbk')
#						if content.find('暂无记录') > -1 or content.find('<font color="black">下一页</font>') >-1 :
#							print(u'本城市结束 跳转到下一城市\n\n')
#							break
				else:
					print(u'已经下载 跳过', file)
			#except:
				print(u'连接错误')
			
			#time.sleep(0.1);
		else:
				print(uself.name,'结束')

##########################################
print('importing data...')
queue = Queue.Queue()
conn = MySQLdb.connect(host='localhost',user='root',db='aibang',charset='utf8')
cur = conn.cursor()
cur.execute("select id,keyword,url from url order by id desc limit 100")
for r in cur.fetchall():
	queue.put(r)

threads = []
for i in range(1):
	threads.append(myThread(queue))

for thread in threads:
	thread.start()

for thread in threads:
	thread.join()

winsound.Beep(1000,500)
#input('程序运行结束，按回车键退出！')