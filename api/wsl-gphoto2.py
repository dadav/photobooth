#!/usr/bin/env python

import sys
import subprocess
from pathlib import Path


p = Path(sys.argv[1])
subprocess.run(
    ["wsl", "gphoto2", "--capture-image-and-download", "--filename", p.name],
    cwd=p.parent,
)
