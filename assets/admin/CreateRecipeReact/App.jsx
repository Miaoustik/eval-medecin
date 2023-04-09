import React, {useCallback, useEffect, useRef, useState} from "react";
import useInputTitle from "./Hooks/useInputTitle";
import useFetch from "./Hooks/useFetch";
import InputWithValidation from "./Components/InputWithValidation";
import useInputDescription from "./Hooks/useInputDescription";
import useCheckboxList from "./Hooks/useCheckboxList";
import InputCheckboxList from "./Components/InputCheckboxList";
import useInputsPreps from "./Hooks/useInputsPreps";
import Input from "./Components/Input";
import useInputGroup from "./Hooks/useInputGroup";
import InputGroup from "./Components/InputGroup";
import InputGroupList from "./Components/InputGroupList";
import useInputGroupIngredient from "./Hooks/useInputGroupIngredient";
import useInputListStages from "./Hooks/useInputListStages";
import InputList from "./Components/InputList";
import useHandleSubmitRecipe from "./Hooks/useHandleSubmitRecipe";
import useFetchRecipe from "./Hooks/useFetchRecipe";
import useCheckboxPatientOnly from "./Hooks/useCheckboxPatientOnly";
import useSignalController from "./Hooks/useSignalController";

const ingredientsPlaceholder = [
    '5 gr',
    'Tomate'
]

export default function ({recipeid = null}) {

    const controllerRef = useSignalController()
    const errorRef = useRef(0)


    const titleProps = useInputTitle(errorRef)
    const descriptionProps = useInputDescription(errorRef)

    const {
        data,
        loading,
        recipeId,
        setRecipeId,
        setRefresh
    } = useFetchRecipe(recipeid, controllerRef)

    const allergensProps = useCheckboxList([])
    const dietsProps = useCheckboxList([])
    const prepsProps = useInputsPreps()

    const ingredientsProps = useInputGroupIngredient(errorRef)
    const stageProps = useInputListStages(errorRef)

    const patientOnlyProps = useCheckboxPatientOnly()

    const {
        handleTryNo,
        submitted,
        handleTrySubmit,
        postLoading,
        trySubmitted,
        handleTryYes,
        handleResetForm
    } = useHandleSubmitRecipe(
        patientOnlyProps,
        titleProps,
        descriptionProps,
        allergensProps,
        dietsProps,
        prepsProps,
        ingredientsProps,
        stageProps,
        controllerRef,
        errorRef,
        recipeId,
        setRefresh,
        recipeid,
        setRecipeId
    )

    useEffect(() => {
        if (data) {

            const allergens = data[1]
            const diets = data[0]

            allergensProps.setState(prevState => {
                const newState = {...prevState}
                allergens.forEach((allergen) => {
                    newState[allergen.name] = false
                })
                return newState
            })

            dietsProps.setState(prevState => {
                const newState = {...prevState}
                diets.forEach(diet => {
                    newState[diet.name] = false
                })
                return newState
            })

            if (recipeid) {
                const recipe = data[2]

                titleProps.setState(prevState => {
                    const newState = {...prevState}
                    newState.value = recipe.title
                    return newState
                })

                descriptionProps.setState(prevState => {
                    const newState = {...prevState}
                    newState.value = recipe.description
                    return newState
                })

                allergensProps.setState(prevState => {
                    const newState = {...prevState}
                    Object.keys(newState).forEach(s => {
                        recipe.allergens.forEach(a => {
                            if (a.name === s) {
                                newState[s] = true
                            }
                        })
                    })
                    return newState
                })

                dietsProps.setState(prevState => {
                    const newState = {...prevState}
                    Object.keys(newState).forEach(s => {
                        recipe.diets.forEach(d => {
                            if (d.name === s) {
                                newState[s] = true
                            }
                        })
                    })
                    return newState
                })

                prepsProps.setState(prevState => {
                    const newState = {...prevState}

                    newState.preparation = recipe.preparationTime
                    newState.cuisson = recipe.cookingTime
                    newState.repos = recipe.breakTime

                    return newState
                })

                ingredientsProps.setState(prevState => {
                    const newState = [...prevState]
                    recipe.ingredientRecipes.forEach((r, i) => {
                        if (i === 0) {
                            newState[0].quantity = r.quantity
                            newState[0].name = r.ingredient.name
                        } else {
                            newState.push({
                                id: i + 1,
                                quantity: r.quantity,
                                name: r.ingredient.name,
                                error: ''
                            })
                        }

                    })
                    return newState
                })

                stageProps.setState(prevState => {
                    const newState = prevState
                    recipe.stages.forEach((s, i) => {
                        if (i === 0) {
                            newState[0].value = s
                        } else {
                            newState.push({
                                id: i + 1,
                                value: s,
                                error: '',
                                nameId: 'stage'
                            })
                        }
                    })
                    return newState
                })
                patientOnlyProps.setState(recipe.patientOnly)
            }
        }
    }, [data])



    if (loading) {
        return (
            <div className="spinner-border" role="status">
                <span className="visually-hidden">Loading...</span>
            </div>
        )
    }

    return (
        <>
            <h2 className="secondTitle text-decoration-underline">{recipeid ? 'Modifier' : 'Créer'} une recette</h2>

            <form onSubmit={handleTryYes}>


                <InputWithValidation required={true} {...titleProps} label={'Titre de la recette : '} />
                <InputWithValidation required={true} {...descriptionProps} type={'textarea'} label={'Description de la recette : '} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Allergies de la recette : </h3>
                <InputCheckboxList {...allergensProps} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Régimes de la recette : </h3>
                <InputCheckboxList {...dietsProps} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Temps de la recette : </h3>

                <p className={'text-secondary text-decoration-underline'}>Temps total : {(parseInt(prepsProps.state.preparation === '' ? 0 : prepsProps.state.preparation, 10) + parseInt(prepsProps.state.repos === '' ? 0 : prepsProps.state.repos, 10) + parseInt(prepsProps.state.cuisson === '' ? 0 : prepsProps.state.cuisson, 10))} minutes</p>

                {Object.keys(prepsProps.state).map(e => {
                    let label = ''

                    if (e === 'preparation') {
                        label = "Temps de préparation : "
                    } else if (e === 'repos') {
                        label = "Temps de repos : "
                    } else {
                        label = "Temps de cuisson : "
                    }

                    return <Input key={e} value={prepsProps.state[e]} name={e} label={label} type={'number'} handleChange={prepsProps.handleChange}/>
                })}

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Ingrédients : </h3>

                <InputGroupList required={true} validationOn={ingredientsProps.validationOn} handleBlur={ingredientsProps.handleBlur} state={ingredientsProps.state} handleRemove={ingredientsProps.handleRemove} ingredientsPlaceholder={ingredientsPlaceholder} addBtnText={'Ajouter un ingrédient'} handleChange={ingredientsProps.handleChange} handleAdd={ingredientsProps.handleAdd}/>

                <h3 className="mt-5 mb-4 secondTitle text-decoration-underline">Etapes : </h3>

                <InputList required={true} {...stageProps} rows={3} addBtnText={'Ajouter une étape'} />

                <h3 className="mt-5 mb-4 secondTitle text-decoration-underline">Visibilité : </h3>
                <div className="ps-5 py-1 form-check">
                    <input type={'checkbox'} checked={patientOnlyProps.state} className="form-check-input" onChange={patientOnlyProps.handleChange} id={'patient'} />
                    <label htmlFor={'patient'} className="form-check-label text-secondary textNoto ps-2">Visible uniquement par les patients</label>
                </div>

                {trySubmitted ? (
                    <>

                        {submitted ? (
                            <>

                                <div className={'alert alert-success shadow1 mt-4'}>
                                    <p className={'p-0 m-0 textNoto'}>Votre recette a bien été {!recipeid ? 'crée' : 'modifiée'}.</p>
                                </div>

                                {recipeid
                                    ? <button onClick={handleTryYes} className={'btn btn-primary text-white w-100 my-3 shadow1'}>Modifier la recette</button>
                                    : <a href={'/admin/modifier-recette/' + recipeId} className={'btn btn-primary text-white w-100 my-3 shadow1'}>Modifier la recette</a>
                                }

                                <p className={'textNoto'}>Voulez-vous créer une nouvelle recette ?</p>
                                {recipeid
                                    ? <a href={'/admin/creer-recette'} className={'btn btn-primary text-white w-100 text-decoration-none shadow1'}>Créer une nouvelle recette</a>
                                    : <button onClick={handleResetForm} className={'btn btn-primary text-white w-100 shadow1'}>Créer une nouvelle recette</button>
                                }

                            </>
                        ) : (

                            <>
                                {trySubmitted && postLoading &&
                                    <div className={'alert alert-success shadow1 mt-4'}>
                                        <p className={'p-0 m-0 textNoto'}>Votre recette est en cours de {recipeid ? 'modification' : 'création'}.</p>
                                    </div>
                                }
                                {postLoading === false &&
                                    <>
                                        <p className={'textNoto mt-3'}>La recette va être {recipeid ? 'modifiée' : 'crée'}. Etes-vous sur? </p>
                                        <div className={'d-inline-flex w-100'}>
                                            <button type={'submit'} className={'btn btn-primary text-white me-3 w-100 shadow1'}>Oui</button>
                                            <button onClick={handleTryNo} className={'btn btn-secondary w-100 shadow1'}>Non</button>
                                        </div>
                                    </>

                                }

                            </>
                        )}

                    </>
                ) : (
                    <button onClick={handleTrySubmit} className={'btn btn-primary text-white w-100 mt-5 shadow1'}>{recipeid ? 'Modifier la recette' : 'Créer la recette'}</button>
                )}
            </form>
        </>
    )
}