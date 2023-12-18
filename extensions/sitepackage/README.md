# Hi, I'm Tilda.

**Frontend-Workflow build to make Lifes less complicated.**

Preinstalls ``jquery`` and ``foundation-sites`` per default.

## Available Commands
### Start watch-mode
Default command `npm start` is an alias
```
npm run serve
```
### Build output folder
Files are always served from `./dist` folder
```
npm run build
```
### Create new component
Accepts optional arguments or otherwise prompts the options
```
npm run create [--cname={string} --hasjs={boolean} --serve={boolean}]
```

Available inline arguments:

* `cname` sets the name of the new component
* `hasjs` creates a additional js-file if required
* `serve` starts the server & enters watch-mode

