# groq_api_setup.py

import os
from groq import Groq

# Mengatur kunci API
os.environ['GROQ_API_KEY'] = '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'

# Inisialisasi klien Groq
klien = Groq(
    api_key=os.getenv("GROQ_API_KEY"),
)

def dapatkan_klien_groq():
    return klien
