#coding:GBK
import winsound
import httplib2
import threading
import queue
import os
import time
import MySQLdb

class myThread(threading.Thread):
	def __init__ (self,queue):
		threading.Thread.__init__(self)
		self.queue = queue

	def run (self):
		headers = {'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)'}
		h = httplib2.Http()
		while (not self.queue.empty()):
			try:
				row = self.queue.get()
				print(row,'\n')
				id = str(row[0])
				pid = str(row[1])
				cid = str(row[3])
				for i in range(100):
					page = str(i + 1)
					file = "F:/cache/jxs3/" + id + '_' + pid + '_' + cid + '_' + page + '.html'
#					file = "g:/" + id + '_' + pid + '_' + cid + '_' + page + '.html'
					if not os.path.exists(file):
						url = "http://dealer.autohome.com.cn/pr" + pid + "_c" + cid + "_p" + page + ".html"
						resp, content = h.request(url, 'GET', headers=headers)
						open(file,'wb').write(content)
						print('正在下载', url)
						
						content = content.decode('gbk')
						if content.find('暂无记录') > -1 or content.find('<font color="black">下一页</font>') >-1 :
							print('本城市结束 跳转到下一城市\n\n')
							break
					else:
						print('已经下载 跳过', file)
			except:
				print('连接错误')
			
			time.sleep(0.1);
		else:
				print(self.name,'结束')

##########################################
print('导入数据……')
queue = queue.Queue()
conn = MySQLdb.connect(host='localhost',user='root',db='autohome',charset='gbk')
cur = conn.cursor()
cur.execute('select * from city order by id')
for r in cur.fetchall():
	queue.put(r)

threads = []
for i in range(2):
	threads.append(myThread(queue))

for thread in threads:
	thread.start()

for thread in threads:
	thread.join()

winsound.Beep(1000,500)
#input('程序运行结束，按回车键退出！')