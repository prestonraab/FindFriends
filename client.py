import streamlit as st
from streamlit_geolocation import streamlit_geolocation

with st.form("my_form"):
   name = st.text_input('username')

   # Every form must have a submit button.
   submitted = st.form_submit_button("Submit")
   if submitted:
       st.write("name", name)

if not name:
  st.warning('Please input a name.')
  st.stop()


location = streamlit_geolocation()

st.write(location)

st.write(dir(location))

st.write(location.items())

st.write(location.keys())

st.write(location.values())

st.write('latitude' in location)

if not location['latitude']:
  st.warning('You have not given access to your location.')
  st.stop()
else:
    st.write(location)


