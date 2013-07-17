<?php

$route['*']['/news/:title'] = array('NewsController', 'show_news_by_title',
                                    'match'=> array(
                                                'title'=>'/^[0-9]+$/'
                                            )
                                   );
