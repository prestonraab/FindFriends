import streamlit as st
import geocoder

st.write("""
Testing
""")

g = geocoder.ip('me')
print(g.latlng)

name = st.chat_input("What is your name?")
password = st.chat_input("Enter a word you will remember, or password if you like.")

