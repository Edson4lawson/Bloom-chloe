const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let isDirectory = fs.statSync(dirPath).isDirectory();
        isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
    });
}

let count = 0;
walkDir('./src', function(filePath) {
    if (filePath.endsWith('.vue')) {
        let content = fs.readFileSync(filePath, 'utf8');
        if (content.includes('pink-')) {
            // Replaces pink-500 with purple-600 to match the main brand color,
            // and all other pink-X with purple-X.
            let newContent = content
                .replace(/pink-500/g, 'purple-600')
                .replace(/pink-/g, 'purple-');
            
            fs.writeFileSync(filePath, newContent, 'utf8');
            console.log(`Updated ${filePath}`);
            count++;
        }
    }
});

console.log(`Done. Updated ${count} files.`);
