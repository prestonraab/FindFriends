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

with st.form("my_form"):
   name = st.chat_input("What is your name?")
   password = st.chat_input("Enter a word you will remember, or password if you like.")

   slider_val = st.slider("Form slider")
   checkbox_val = st.checkbox("Form checkbox")

   # Every form must have a submit button.
   submitted = st.form_submit_button("Submit")
   if submitted:
       st.write("slider", slider_val, "checkbox", checkbox_val)

st.write("Outside the form")

st.write(name)
