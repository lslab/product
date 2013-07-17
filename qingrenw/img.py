#coding:UTF-8
import winsound
import httplib2
import threading
import queue
import os
import time

class myThread(threading.Thread):
	def __init__ (self,queue):
		threading.Thread.__init__(self)
		self.queue = queue

	def run (self):
		headers = {'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)'}
		#h = httplib2.Http(proxy_info = httplib2.ProxyInfo(socks.PROXY_TYPE_SOCKS5, 'localhost', 8580))
		h = httplib2.Http()
		headers = {'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)'}
		while (not self.queue.empty()):
			try:
				row = self.queue.get()
				#print(urow)
				file = str(row[0])
				url = row[1].strip()
				if not os.path.exists(file):
					resp, content = h.request(url, 'GET', headers=headers)
					open(file,'wb').write(content)
					print('正在下载', url)
					
					#content = content.decode('gbk')
#						if content.find('暂无记录') > -1 or content.find('<font color="black">下一页</font>') >-1 :
#							print(u'本城市结束 跳转到下一城市\n\n')
#							break
				else:
					print('已经下载 跳过', file)
			except:
				print('连接错误')
			
			#time.sleep(0.1);
		else:
				print(uself.name,'结束')

##########################################
print('导入数据……')
queue = queue.Queue()
for r in open('D:/product/qingrenw/img.txt','r'):
	row=r.split('\t')
	queue.put(row)

threads = []
for i in range(10):
	threads.append(myThread(queue))

for thread in threads:
	thread.start()

for thread in threads:
	thread.join()

winsound.Beep(1000,500)
#input('程序运行结束，按回车键退出！')