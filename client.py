import streamlit as st
import geocoder
from streamlit_geolocation import streamlit_geolocation

st.write("""
Testing
""")

g = geocoder.ip('me')
st.write(g.latlng)


location = streamlit_geolocation()

st.write(location)

name = st.chat_input("What is your name?")
password = st.chat_input("Enter a word you will remember, or password if you like.")

