export function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

export function inputSetError (setter, notValidBool, errorRef, message = 'Not good', index = null, byId = false, withGood = true) {
    if (byId) {
        setter(prevState => {
            const newState = [...prevState]

            newState.forEach(s => {
                if (s.id == index) {
                    if (notValidBool) {
                        if (s.error !== message) {
                            errorRef.current++
                            s.error = message
                        }
                    } else {
                        if (s.error === message) {
                            s.error = withGood ? 'ok' : ''
                            errorRef.current--
                        }
                    }
                }

            })

            return newState
        })
    } else if (index) {
        setter(prevState => {
            const newState = [...prevState]
            if (notValidBool) {
                if (newState[index].error !== message) {
                    errorRef.current++
                    newState[index].error = message
                }
            } else {
                if (newState[index].error === message) {
                    newState[index].error = withGood ? 'ok' : ''
                    errorRef.current--
                }
            }
            return newState
        })
    } else {
        setter(prevState => {
            const newState = {...prevState}
            if (notValidBool) {
                if (newState.error !== message) {
                    errorRef.current++
                    newState.error = message
                }
            } else {
                if (newState.error === message) {
                    newState.error = withGood ? 'ok' : ''
                    errorRef.current--
                }
            }
            return newState
        })
    }
}

export function arrayCheckDuplicate(state, value, setState, message, errorRef) {

    let error = ''

    state.forEach((stateObj, index) => {

       if (stateObj.name.trim().toLowerCase() === value.trim().toLowerCase()) {
           error = message
           setState(prevState => {
               const newState = [...prevState]
               if (newState[index].error !== message) {
                   errorRef.current++
                   newState[index].error = message
               }
           })
       } else {

       }
    })

    if (error === '') {
        setState(prevState => {
            const newState = [...prevState]
            if (newState[index].error === message) {
                newState[index].error = 'ok'
                errorRef.current--
            }
        })

    }


}

export function arraySetError1 (stateObj, error) {

    if (stateObj.error !== error) {
        dataRef.current.error++
        inputs.setInputs(prevState => {
            const newState = [...prevState]
            newState[k].error = error
            return newState
        })
    }
    return error
}

export function arraySetError2 () {

}