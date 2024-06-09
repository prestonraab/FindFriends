import streamlit as st
from streamlit_geolocation import streamlit_geolocation
import js_eval


if 'watching' not in st.session_state:
    sucess = js_eval.start_watching_location()
    if sucess:
        st.session_state['watching'] = True
        st.write("Watch succeeded")
    else:
        st.write("Watch failed")

streamlit_geolocation()


if 'location' not in st.session_state:
    location = js_eval.get_first_location()
    if location:
        st.session_state['location'] = location
        st.rerun()
    st.warning('You have not given access to your location.')
    st.stop()


iter = 1

def frequent_get_location():
    global iter
    location = js_eval.get_latest_location()
    st.write(iter)
    st.write(st.session_state['location'])
    if location:
        st.session_state['location'] = location
    else:
        location = js_eval.get_first_location()
        st.write(location)


get_location = st.experimental_fragment(frequent_get_location, run_every=1)
get_location()
