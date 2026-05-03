import urllib.request
import os

os.makedirs(r'f:\Antrix\antrix\assets\images', exist_ok=True)

images = [
    'chandrayaan.jpg', 'perseid.jpg', 'solar-eclipse.jpg', 'iss.jpg', 'starship.jpg',
    'mars.jpg', 'lunar-eclipse.jpg', 'jupiter.jpg', 'geminid.jpg', 'pslv.jpg',
    'gaganyaan.jpg', 'jwst.jpg', 'spacex-news.jpg', 'aditya-l1.jpg', 'artemis.jpg',
    'gallery-earth.jpg', 'gallery-falcon.jpg', 'gallery-orion.jpg', 'gallery-iss-moon.jpg',
    'gallery-saturn.jpg', 'gallery-sls.jpg', 'gallery-crab.jpg', 'gallery-eva.jpg'
]

for img in images:
    name = img.replace('.jpg', '').replace('-', ' ').title()
    url = f'https://placehold.co/800x600/0a0e17/00e5ff.png?text={name.replace(" ", "+")}'
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req) as response, open(os.path.join(r'f:\Antrix\antrix\assets\images', img), 'wb') as out_file:
            data = response.read()
            out_file.write(data)
        print(f'Downloaded {img}')
    except Exception as e:
        print(f'Failed {img}: {e}')
