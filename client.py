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
   name = st.text_input('Name')

   # Every form must have a submit button.
   submitted = st.form_submit_button("Submit")
   if submitted:
       st.write("name", name)

if not name:
  st.warning('Please input a name.')
  st.stop()

st.write("Outside the form")

