const https = require('https');
const fs = require('fs');

https.get('https://gist.githubusercontent.com/juanbrujo/0fd2f4d126b3ce5a95a7dd1f28b3d8dd/raw/b8575eb82dce974fd2647f46819a7568278396bd/comunas-regiones.json', (res) => {
    let data = '';
    res.on('data', chunk => data += chunk);
    res.on('end', () => {
        const parsed = JSON.parse(data);
        const communes = [];
        parsed.regions.forEach(region => {
            region.communes.forEach(commune => {
                communes.push(commune.name);
            });
        });
        fs.writeFileSync('comunas.json', JSON.stringify(communes));
    });
});
